<?php

$spec = Pearfarm_PackageSpec::create(array(Pearfarm_PackageSpec::OPT_BASEDIR => dirname(__FILE__)))
             ->setName('tuisongbao')
             ->setChannel('tuisongbao.pearfarm.org')
             ->setSummary('tuisongbao SDK')
             ->setDescription('tuisongbao SDK')
             ->setReleaseVersion('0.9.0')
             ->setReleaseStability('alpha')
             ->setApiVersion('0.9.0')
             ->setApiStability('alpha')
             ->setLicense(Pearfarm_PackageSpec::LICENSE_APACHE)
             ->setNotes('Initial release.')
             ->addMaintainer('lead', 'www.tuisongbao.com', 'tuisongbao', 'support@tuisongbao.com')
             ->addGitFiles()
             ;