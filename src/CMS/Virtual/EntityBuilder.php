<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

use RuntimeException;
use stdClass;

abstract class EntityBuilder
{

    /**
     * Constructing an IVirtualEntity by constructing a ConvertedEntity based on the provided stdClass.
     *
     * @param stdClass $object
     *
     * @return IVirtualPageEntity
     * @throws RuntimeException
     * @see ConvertedPageEntity
     */
    public static function constructVirtualEntity(stdClass $object): IVirtualPageEntity
    {

        /*
         * Using the solution from https://stackoverflow.com/a/3243949/10379838
         * to do this conversion.
         */
        $strstr = strstr(serialize($object), '"');

        if (!is_string($strstr)) {
            throw new RuntimeException(
                'Could not construct virtual entity, first occurrence of ' .
                '\'"\' could not be found in serialized object'
            );
        }

        return unserialize(
            sprintf(
                'O:%d:"%s"%s',
                strlen(ConvertedPageEntity::class),
                ConvertedPageEntity::class,
                strstr($strstr, ':')
            )
        );
    }
}
