<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class FinalNode extends LeafNode
{

    final public function isFinal(): bool
    {
        return true;
    }
}
