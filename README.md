# User Bundle

makg/user-bundle provides minimum model to store users, along with optional additional features. It supports only Symfony 4.x and Doctrine2 ORM.

Author: Maciej Gierej - http://maciej.gierej.pl

Features:
- Customizable registration process
- Customizable password resetting process
- Login form with simple, styled layout
- Styled e-mail templates with both HTML and plain text bodies
- `AvatarTrait` to store uploaded or generated avatars
- `ConfigurableTrait` to handle i.e. first-time login
- Symfony Commands to create users, changing password, handling roles, (de)activating, regenerating avatars

There are other bundles providing similar functionalities, the most popular being FOSUserBundle. You should decide which one is more appropriate for your project. The key differences between makg/user-bundle and friendsofsymfony/user-bundle:
- makg/user-bundle supports only Symfony 4.x and Doctrine2 ORM to avoid maintenance of some BC tweaks
- makg/user-bundle provides only minimum user model, it's missing some features available in FOSUserBundle: username (login only via e-mail), canonical fields (e-mail is transformed to lowercase), salt (some algorithms like bcrypt store salt in hash, so it's not always needed), last_login.
- makg/user-bundle has no show/edit profile controllers
- makg/user-bundle could be an out of the box solution if you need simple authorization process with user-friendly layout, forms and e-mails

## Installation

### Step 1: Install UserBundle using Composer
```
composer req makg/user-bundle
```

### Step 2: Enable the bundle

### Step 3: Create User class

```php
<?php
// src/Entity/User.php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends \MakG\UserBundle\Entity\User
{

}

```

### Step 4: Configure security.yaml

```yaml
# /config/packages/security.yaml

security:
    password_hashers:
        App\Users\Entity\User: 'bcrypt'
    providers:
        makg_userbundle:
            id: makg_user.user_provider
    firewalls:
        main:
            anonymous: ~
            user_checker: MakG\UserBundle\Security\UserChecker
            logout:
                path: logout
            form_login:
                provider: makg_userbundle
                csrf_token_generator: security.csrf.token_manager
                login_path: mg_user_security_login
                check_path: mg_user_security_login
            remember_me:
                secret: '%kernel.secret%'
                lifetime: '%env(REMEMBER_ME_LIFETIME)%'
                path: /
```

### Step 5: Configure User Bundle

```yaml
# /config/packages/makg_user.yaml

makg_user:
    user_class: App\Entity\User
```

### Step 6: Add routes

```yaml
# /config/routes.yaml

makg_user:
    resource: '@UserBundle/Resources/config/routing/all.yaml'

logout:
    path: /logout

# or if you don't need all the features, you can import the routes separately:
# makg_user_login:
#     resource: '@UserBundle/Resources/config/routing/login.yaml'
# makg_user_registration:
#     resource: '@UserBundle/Resources/config/routing/registration.yaml'
# makg_user_resetting:
#     resource: '@UserBundle/Resources/config/routing/resetting.yaml'
```

## Commands

```
makg:user:create [--inactive|--send-confirmation-email|--send-resetting-email] <email>
makg:user:change-password [--random] <email> [<password>]
makg:user:roles [--append|--delete] <email>
makg:user:update [--regenerate-avatar|--activate|--deactivate] <email>
makg:user:promote <email> [<roles>...]
makg:user:demote <email> [<roles>...]
```

The last two (`makg:user:promote` and `makg:user:demote`) are aliases to `makg:user:roles` commands, as a courtesy to people also working with FOSUserBundle.


## Overriding services

All services inside this bundle that implement custom interfaces may be overridden by simply creating an alias for those interfaces. Example:

```yaml
# /config/services.yaml
MakG\UserBundle\AvatarGenerator\AvatarGeneratorInterface:
        alias: 'App\User\CustomAvatarGenerator'

MakG\UserBundle\Mailer\MailerInterface:
    alias: 'App\Mail\CustomMailer'

MakG\UserBundle\Manager\UserManagerInterface:
    alias: 'App\User\CustomUserManager'

MakG\UserBundle\Manager\UserManipulatorInterface:
    alias: 'App\User\CustomUserManipulator'
```

## Optional features

### Automatically login user on completing registration and resetting password

```yaml
# /config/services.yaml
imports:
    - { resource: '@UserBundle/Resources/config/auth_user_auth.yaml' }
```

### Flash messages on completing registration and resetting password

```yaml
# /config/services.yaml
imports:
    - { resource: '@UserBundle/Resources/config/flash_messages.yaml' }
```

### User avatars

`AvatarTrait` provides field for storing path to the avatar and a field for handling uploaded/generated files.

This bundle does not provide upload handling, you may use `VichUploaderBundle` for that. `MakG\Manager\UserManager::updateUser` provides workaround for VichUploaderBundle when the avatar is uploaded without any other changes to the entity. Normally, VichUploaderBundle fails to update field with path to image when there are no other changes.

```php
<?php
// src/Entity/User.php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use MakG\UserBundle\Entity\AvatarTrait;

/**
 * @ORM\Entity()
 */
class User extends \MakG\UserBundle\Entity\User
{
    use AvatarTrait;
}
```

### Easy Admin integration

This bundle provides simple integration with [EasyAdminBundle](https://github.com/EasyCorp/EasyAdminBundle). It provides event subscriber handling updating user and yaml configuration for EasyAdmin.

To enable this integration, you need to import configuration files in your `services.yaml`, `makg_user.yaml` or `easy_admin.yaml` files.

```
# /config/services.yaml

imports:
    - { resource: '@UserBundle/Resources/config/easy_admin_integration/services.yaml' }
```

```
# /config/easy_admin.yaml

imports:
    - { resource: '@UserBundle/Resources/config/easy_admin_integration/easy_admin_entity.yaml' }
```

> IMPORTANT: In your /config/bundles.php file, UserBundle has to be added before EasyAdminBundle, as the integration uses `makg_user.user_class` parameter. Otherwise you may get `You have requested a non-existent parameter "makg_user.user_class".` error.
