<?php

use Jackalope\Test\Fixture\DBUnitFixtureXML;
use Jackalope\Test\Fixture\JCRSystemXML;

/**
 * Convert Jackalope Document or System Views into PHPUnit DBUnit Fixture XML files.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author cryptocompress <cryptocompress@googlemail.com>
 */
function generate_fixtures($srcDir, $destDir)
{
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir)) as $srcFile) {
        $extension = $srcFile->getExtension();

        if (!$srcFile->isFile() || 'xml' !== $extension) {
            continue;
        }

        $srcDom = new JCRSystemXML($srcFile->getPathname());
        $nodes = $srcDom->load()->getNodes();
        if ($nodes->length < 1) {
            continue;
        }

        $destDom = new DBUnitFixtureXML(str_replace($srcDir, $destDir, $srcFile->getPathname()));
        $destDom->addDataset();
        $destDom->addWorkspace('tests');
        $destDom->addNamespaces($srcDom->getNamespaces());
        $destDom->addNodes('tests', $nodes);
        // delay this to the end to not add entries for weak refs to not existing nodes
        $destDom->addReferences();
        $destDom->save();
    }
}
