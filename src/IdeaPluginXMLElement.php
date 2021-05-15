<?php

namespace Elabee\Phpstorm\Swoole\Plugin;

/**
 * Class IdeaPluginXMLElement
 *
 * @package Elabee\Phpstorm\Swoole\Plugin
 *
 * @property string $version
 */
class IdeaPluginXMLElement extends \SimpleXMLElement
{

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function setChangeNotes(string $data): static
    {
        $dom = dom_import_simplexml($this->{'change-notes'});
        assert($dom->firstChild instanceof \DOMCdataSection);
        $dom->firstChild->replaceData(5, strlen(trim($dom->firstChild->data)), $data);

        return $this;
    }

}
