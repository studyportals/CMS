<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

use StudyPortals\Utils\HTTP;

final class VirtualPath
{

    /*
     * Masks used to enable storing a type selector and two ID's in a single
     * virtual page ID. This allows modules to display "combined" virtual pages
     * without requiring any major changes to our core system.
     *
     * 01110000000000000000000000000000 Type (0-7)
     * 00001111111111111100000000000000 ID1 (0-16383)
     * 00000000000000000011111111111111 ID2 (0-16383)
     */

    public const MASK_TYPE = 0x70000000;
    public const MASK_ID1 = 0x0FFFC000;
    public const MASK_ID2 = 0x00003FFF;

    public const TYPE_MAX_SIZE = 7;
    public const ID1_MAX_SIZE = 16383;
    public const ID2_MAX_SIZE = 16383;

    /**
     * Explode a Virtual Path into its components.
     *
     * <p>Returns an array containing the following virtual path elements:</p>
     * <ul>
     *        <li><b>path:</b> Path the to PageTreeNode. Both the leading and
     *        the trailing slash are stripped.</li>
     *        <li><b>locale:</b> Locale, e.g. en_GB <i>(optional)</i></li>
     *        <li><b>id:</b> Virtual Page ID <i>(optional)</i></li>
     *        <li><b>name:</b> Virtual Page Name <i>(optional)</i>. The virtual
     *        file extension ".html" is stripped from this name.</li>
     * </ul>
     *
     * @param string $virtual_path
     *
     * @return array<string>
     * @throws VirtualPathException
     */

    public static function explodeVirtualPath(
        string $virtual_path
    ): array {
        $virtual_path = filter_var($virtual_path, FILTER_SANITIZE_URL);

        if ($virtual_path === false) {
            throw new VirtualPathException('Virtualpath is not a URL');
        }

        $virtual_path = strtolower((string) $virtual_path);

        $virtual_path = trim($virtual_path, '/');

        if ($virtual_path === '') {
            return [];
        }

        return explode('/', $virtual_path);
    }

    /**
     * Pack a type selector and two ID's into a single virtual page ID.
     *
     * <p>"Type" allows multiple packed IDs of different kinds on a single
     * page.</p>
     * <p>
     *    Example case:<br>
     *    Imagine the following URL:<br>
     * <br>
     * http://www.prtl.eu/landing-page/268599316/landing-page.html
     * <br>
     * Unpacked, it results in:
     * <br>
     * <ul>
     *    <li>type = 1</li>
     *    <li>id1 = 10</li>
     *    <li>id2 = 20</li>
     * </ul>
     * <br>
     * <p>
     *    There is an assumption that type 1 stands for a combination of a
     *    Country ID and a Discipline ID, we now know what to retrieve.
     * </p>
     * <br>
     * Another URL may be
     * <br>
     * http://www.prtl.eu/landing-page/537034772
     * <br>
     * Unpacked, it results in:
     * <br>
     * <ul>
     *    <li>type = 2</li>
     *    <li>id1 = 10</li>
     *    <li>id2 = 20</li>
     * </ul>
     * <br>
     * <p>It is then possible to put two VirtualPage modules on a single URL
     * where one reacts to type 1 and the other reacts to type 2. However, if
     * we
     * use distinct URLs like:<br>
     * http://www.prtl.eu/landing-type-1/12345<br>
     * and<br>
     * http://www.prtl.eu/landing-type-2/12345<br>
     * the type effectively does not play that much of a role anymore.<br>
     * <br>
     * </p>
     * <p>
     *    A list of types defined as constant can be found in the project's own
     *    Singleton.
     * </p>
     *
     * @param integer $type
     * @param integer $id1
     * @param integer $id2
     *
     * @return integer
     */

    public static function packVirtualID($type, $id1, $id2): int
    {

        assert('$type >= 0	&& $type <= self::TYPE_MAX_SIZE');
        assert('$id1 >= 0	&& $id1 <= self::ID1_MAX_SIZE');
        assert('$id2 >= 0	&& $id2 <= self::ID2_MAX_SIZE');

        $type = self::MASK_TYPE & ($type << 28);
        $id1 = self::MASK_ID1 & ($id1 << 14);
        $id2 = self::MASK_ID2 & $id2;

        return $type ^ $id1 ^ $id2;
    }

    /**
     * Unpack a "packed" virtual page ID into its type selector and ID's.
     *
     * @param integer $virtual_id
     *
     * @return array<string,int>
     */

    public static function unpackVirtualID($virtual_id): array
    {

        $type = (self::MASK_TYPE & $virtual_id) >> 28;
        $id1 = (self::MASK_ID1 & $virtual_id) >> 14;
        $id2 = self::MASK_ID2 & $virtual_id;

        return [
            'type' => $type,
            'id1' => $id1,
            'id2' => $id2,
        ];
    }

    /**
     * Format the given string as a valid (virtual) page name.
     *
     * Removes all illegal characters and replaces spaces by "-".
     *
     * @param string $name
     * @param string $extension
     *
     * @return string
     */

    public static function formatVirtualPageName(
        string $name,
        string $extension = '.html'
    ): string {

        $name = HTTP::formatURLPathPart($name);
        return "$name$extension";
    }

    /**
     * Function packCompoundVirtualID.
     *
     * @param array<int> $ids
     *
     * @return string
     */

    public static function packCompoundVirtualID(array $ids): string
    {

        return implode('-', $ids);
    }

    /**
     * Function unpackCompoundVirtualID.
     *
     * @param string $virtual_id
     *
     * @return array<string>
     */

    public static function unpackCompoundVirtualID(string $virtual_id): array
    {

        return explode('-', $virtual_id);
    }
}
