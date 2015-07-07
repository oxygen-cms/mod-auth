<?php

use Illuminate\Database\Migrations\Migration;
use Oxygen\Preferences\Loader\Database\PreferenceRepositoryInterface;
use Oxygen\Preferences\Repository;

class CreateAuthPreferences extends Migration {

    /**
     * Run the migrations.
     *
     * @param \Oxygen\Preferences\Loader\Database\PreferenceRepositoryInterface $preferences
     */
    public function up(PreferenceRepositoryInterface $preferences) {
        $item = $preferences->make();
        $item->setKey('appearance.auth');
        $data = new Repository([]);
        $data->set('theme', 'autumn');
        $item->setPreferences($data);
        $preferences->persist($item, false);

        $item = $preferences->make();
        $item->setKey('modules.auth');
        $data = new Repository([]);
        $item->setPreferences($data);
        $preferences->persist($item, false);

        $preferences->flush();
    }

    /**
     * Reverse the migrations.
     *
     * @param \Oxygen\Preferences\Loader\Database\PreferenceRepositoryInterface $preferences
     */
    public function down(PreferenceRepositoryInterface $preferences) {
        $preferences->delete($preferences->findByKey('appearance.auth'));
        $preferences->delete($preferences->findByKey('modules.auth'));
    }
}
