<?php

namespace VS7\PromoWidget\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Category\FileInfo;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_fileInfo;

    public function __construct(
        Context $context,
        FileInfo $flleInfo
    )
    {
        $this->_fileInfo = $flleInfo;
        parent::__construct($context);
    }

    public function prepareImage($fileName)
    {
        if (empty($fileName)) return null;
        $imageData = array();
        if ($this->_fileInfo->isExist($fileName)) {
            $stat = $this->_fileInfo->getStat($fileName);
            $mime = $this->_fileInfo->getMimeType($fileName);
            $imageData['name'] = basename($fileName);

            if ($this->_fileInfo->isBeginsWithMediaDirectoryPath($fileName)) {
                $imageData['url'] = $fileName;
            } else {
                $imageData['url'] = '';
            }

            $imageData['size'] = isset($stat) ? $stat['size'] : 0;
            $imageData['type'] = $mime;
        }

        return $imageData;
    }
}
