<?php
namespace Phalconeer\FormValidator\Test\Mock;

use Phalcon\Config as PhalconConfig;

class ConfigMock extends PhalconConfig\Config
{
    public function __construct()
    {
        parent::__construct([
            'dateFormats'   => [
                'Y-m-d',
                'Y.m.d.',
                'Ymd',
                'Y/m/d',
                'd-m-Y',
                'd.m.Y.',
                'd/m/Y',
            ],
            'timeFormats'       => [
                'Y-m-d H:i:s',
                'Y-m-d H:i:s.u',
                'Y.m.d. H:i:s',
                'Y.m.d. H:i:s.u',
                'Ymd His',
                'Ymd His.u',
                'Y/m/d H:i:s',
                'Y/m/d H:i:s.u',
                'd-m-Y H:i:s',
                'd-m-Y H:i:s.u',
                'd.m.Y. H:i:s',
                'd.m.Y. H:i:s.u',
                'd/m/Y H:i:s',
                'd/m/Y H:i:s.u',
                'Y-m-d\TH:i:s',
                'Y-m-d\TH:i:s.u',
                'Y-m-d\TH:i:s.u\Z',
                'Ymd\THis',
                'Ymd\THis.u',
                'd/m/Y\TH:i:s',
                'd/m/Y\TH:i:s.u',
                'Y-m-d\THis',
                'Y-m-d\THis.u',
                'd/m/Y\THis',
                'd/m/Y\THis.u',
            ]
        ]);
    }
}