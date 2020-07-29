<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

use StudyPortals\CMS\Virtual\VirtualPath;

/**
 * VirtualEntityModule.
 *
 * <p>This interface is constructed around the assumptation that whenever a
 * certain virtual page is requested, the VirtualPageModule handling the request
 * will instantiate an "entity" representing the virtual page (e.g. a news item,
 * article or document). As some other modules on the Page might be interested
 * to know which entity got loaded, the VirtualEntityModule and
 * VirtualEntityDependentModule interfaces are defined.</p>
 *
 * <p>Together, these interfaces allow for an optimised and streamlined transfer
 * of the "entity" loaded by the VirtualEntityModule (and by extend the
 * VirtualPageModule) to any other "interested"modules (which are thus required
 * to implement the VirtualEntityDependentModule interface).</p>
 */
interface VirtualEntityModule
{

    /**
     * Set the virtual-page ID for the Module.
     *
     * <p>In case the Module is unable to set the virtual-page ID as provided,
     * an exception of type ModuleException should be thrown. In this case, the
     * CMS will signal an HTTP 404 error. In all other cases, an HTTP 500 error
     * will be signalled to the visitor.</p>
     *
     * @param string $id
     *
     * @return void
     * @throws ModuleException
     */

    public function setVirtualPageID(string $id);

    /**
     * Get virtual-page name expected by the Module.
     *
     * <p><strong>Important:</strong> This method should provide the required
     * virtual page name in an unformatted manner (c.q. without being passed
     * through {@see VirtualPath::formatVirtualPageName()}).</p>
     *
     * @return string
     * @see VirtualPath::formatVirtualPageName()
     */

    public function getVirtualPageName();

    /**
     * Get the "entity" loaded for the current virtual page.
     *
     * <p><strong>Note:</strong> The entity returned should be an object. If
     * anything else is returned, it is ignored.</p>
     *
     * @return object
     * @see VirtualPageDependentModule
     */

    public function getVirtualEntity();
}
