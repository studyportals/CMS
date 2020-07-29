<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

use StudyPortals\CMS\Page\IPage;

abstract class ControllerModule extends Module implements IControllerModule
{

    /**
     * Display the ControllerModule.
     *
     * <p>This method is final because ControllerModule is not allowed to
     * display any output. {@link IPage::display()} is set up in such a way as
     * to never call this method on ControllerModules. The exception is here
     * just to be absolutely sure...</p>
     *
     * @return string
     * @throws ControllerModuleException
     * @see IPage::display()
     */

    final public function displayMain(): string
    {

        throw new ControllerModuleException(
            'ControllerModule::displayMain() should never be called'
        );
    }
}
