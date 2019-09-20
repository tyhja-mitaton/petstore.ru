<?php


namespace console\controllers;

use Yii;
use yii\console\Controller;
use api\rbac\AuthorRule;
use api\rbac\OwnProfileRule;

class RbacController extends Controller
{
    public function init()
    {
        $authManager = Yii::$app->authManager;
        //$authManager->removeAll();

        //create roles
        $guest  = $authManager->createRole('guest');
        $user  = $authManager->createRole('user');
        $admin  = $authManager->createRole('admin');
        $banned = $authManager->createRole('banned');

        //create permissions
        $login  = $authManager->createPermission('login');
        $logout = $authManager->createPermission('logout');
        $error  = $authManager->createPermission('error');
        $signUp = $authManager->createPermission('sign-up');
        $addPet = $authManager->createPermission('add-pet');
        $updatePet = $authManager->createPermission('update-pet');
        $deletePet = $authManager->createPermission('delete-pet');
        $setStatus = $authManager->createPermission('set-status');
        $changeCategory = $authManager->createPermission('change-category');
        $changeUser = $authManager->createPermission('change-user');
        $banUser = $authManager->createPermission('ban-user');


        //add permissions
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($error);
        $authManager->add($signUp);
        $authManager->add($addPet);
        $authManager->add($updatePet);
        $authManager->add($deletePet);
        $authManager->add($setStatus);
        $authManager->add($changeCategory);
        $authManager->add($changeUser);
        $authManager->add($banUser);

        //add roles
        $authManager->add($guest);
        $authManager->add($user);
        $authManager->add($admin);
        $authManager->add($banned);

        //add permission per role
        $authManager->addChild($guest, $login);
        $authManager->addChild($guest, $logout);
        $authManager->addChild($guest, $error);
        $authManager->addChild($guest, $signUp);
        //user
        $authManager->addChild($user, $addPet);
        $authManager->addChild($user, $deletePet);
        $authManager->addChild($user, $setStatus);
        //admin
        $authManager->addChild($admin, $changeCategory);
        $authManager->addChild($admin, $updatePet);
        $authManager->addChild($admin, $changeUser);
        $authManager->addChild($admin, $banUser);
        $authManager->addChild($admin, $user);
        //banned
        $authManager->addChild($banned, $error);

        $authorRule = new AuthorRule();
        $authManager->add($authorRule);
        $updateOwnPet = $authManager->createPermission('update-own-pet');
        $updateOwnPet->ruleName = $authorRule->name;
        $authManager->add($updateOwnPet);
        $authManager->addChild($updateOwnPet, $updatePet);
        $authManager->addChild($user, $updateOwnPet);

        $ownProfileRule = new OwnProfileRule();
        $authManager->add($ownProfileRule);
        $updateOwnProfile = $authManager->createPermission('update-own-profile');
        $updateOwnProfile->ruleName = $ownProfileRule->name;
        $authManager->add($updateOwnProfile);
        $authManager->addChild($updateOwnProfile, $changeUser);
        $authManager->addChild($user, $updateOwnProfile);

        $authManager->assign($user, 1);
        $authManager->assign($admin, 6);

    }

}