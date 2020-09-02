<?php

namespace VS7\PromoWidget\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;

class Rule
{
    protected $_bannerFactory;
    protected $_request;
    protected $_imageUploader;
    protected $_storeManager;
    protected $_logger;
    protected $_filesystem;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \VS7\PromoWidget\Model\BannerFactory $bannerFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_bannerFactory = $bannerFactory;
        $this->_request = $request;
        $this->_imageUploader = $imageUploader;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
    }

    public function afterSave(
        \Magento\SalesRule\Model\Rule $subject,
        $data
    )
    {
        $postData = $this->_request->getPostValue();
        if (
            $postData
            && isset($postData['vs7_promowidget'])
            && (
                !empty($postData['vs7_promowidget']['name'])
                || !empty($postData['vs7_promowidget']['url_key'])
                || !empty($postData['vs7_promowidget']['image'])
                || !empty($postData['vs7_promowidget']['position'])
                || !empty($postData['vs7_promowidget']['text'])
            )
        ) {
            $banner = $this->_bannerFactory->create();
            $bannerData = $postData['vs7_promowidget'];
            $ruleId = $data->getId();
            $banner->load($ruleId, 'rule_id');
            $bannerData['rule_id'] = $ruleId;
            if ($banner->getId() != null) {
                $bannerData['banner_id'] = $banner->getId();
            }
            if (isset($bannerData['image'])) {
                $bannerData['image'] = $this->processImage($bannerData['image']);
            } else {
                $bannerData['image'] = '';
            }
            $banner->setData($bannerData);
            $banner->save();
        }
        return $data;
    }

    protected function processImage(array $value)
    {
        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            try {
                $store = $this->_storeManager->getStore();
                $baseMediaDir = $store->getBaseMediaDir();
                $newImgRelativePath = $this->_imageUploader->moveFileFromTmp($imageName, true);
                $value[0]['url'] = '/' . $baseMediaDir . '/' . $newImgRelativePath;
                $value[0]['name'] = $value[0]['url'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        } elseif ($this->fileResidesOutsideCategoryDir($value)) {
            $value[0]['url'] = parse_url($value[0]['url'], PHP_URL_PATH);
            $value[0]['name'] = $value[0]['url'];
        }

        if ($imageName = $this->getUploadedImageName($value)) {
            if (!$this->fileResidesOutsideCategoryDir($value)) {
                $imageName = $this->checkUniqueImageName($imageName);
            }
            return $imageName;
        } elseif (!is_string($value)) {
            return null;
        }
    }

    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    private function fileResidesOutsideCategoryDir($value)
    {
        if (!is_array($value) || !isset($value[0]['url'])) {
            return false;
        }

        $fileUrl = ltrim($value[0]['url'], '/');
        $baseMediaDir = $this->_filesystem->getUri(DirectoryList::MEDIA);

        if (!$baseMediaDir) {
            return false;
        }

        return strpos($fileUrl, $baseMediaDir) !== false;
    }

    private function checkUniqueImageName(string $imageName): string
    {
        $mediaDirectory = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $imageAbsolutePath = $mediaDirectory->getAbsolutePath(
            $this->_imageUploader->getBasePath() . DIRECTORY_SEPARATOR . $imageName
        );

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $imageName = call_user_func([Uploader::class, 'getNewFilename'], $imageAbsolutePath);

        return $imageName;
    }
}
