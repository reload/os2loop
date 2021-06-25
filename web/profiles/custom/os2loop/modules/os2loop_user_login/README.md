# OS2Loop user login

Log in via OpenID Connect and SAML.

Go to Administration › Configuration › OS2Loop › OS2Loop user login settings
(`/admin/config/os2loop/os2loop_user_login/settings`) to enable login methods.

## OpenID Connect

The modules [OpenID Connect](https://www.drupal.org/project/openid_connect) and
[OpenID Connect Microsoft Azure Active Directory
client](https://www.drupal.org/project/openid_connect_windows_aad) are used for
OpenID Connect login. In the default configuration both login methods assume
that the identitity provider returns a `name` claim which is used as the Drupal
user name and that a `groups` claim is a list of groups that can be mapped to
Drupal roles.

Any changes to the default configuration can be made in `settings.local.php` as
shown in the following sections.

### Generic

Your identity provider must allow `https://«OS2Loop url»/openid-connect/generic`
as a valid return url.

```php
// web/sites/*/settings.local.php
// Enable Generic OpenID Connect
$config['openid_connect.settings.generic']['enabled'] = 'generic';
$config['openid_connect.settings.generic']['settings']['client_id'] = …; // Get this from your IdP provider
$config['openid_connect.settings.generic']['settings']['client_secret'] = …; // Get this from your IdP provider
$config['openid_connect.settings.generic']['settings']['authorization_endpoint'] = …; // Get this from your IdP provider
$config['openid_connect.settings.generic']['settings']['token_endpoint'] = …; // Get this from your IdP provider
```

### Microsoft Azure Active Directory

Your identity provider must allow `https://«OS2Loop
url»/openid-connect/windows_aad` as a valid return url.

```php
// web/sites/*/settings.local.php
// Enable Windows Azure AD
$config['openid_connect.settings.windows_aad']['enabled'] = 'windows_aad';
$config['openid_connect.settings.windows_aad']['settings']['client_id'] = …; // Get this from your IdP provider
$config['openid_connect.settings.windows_aad']['settings']['client_secret'] = …; // Get this from your IdP provider
$config['openid_connect.settings.windows_aad']['settings']['authorization_endpoint_wa'] = …; // Get this from your OpenID Connect Discovery endpoint
$config['openid_connect.settings.windows_aad']['settings']['token_endpoint_wa'] = …; // Get this from your OpenID Connect Discovery endpoint
$config['openid_connect.settings.windows_aad']['settings']['use_v2'] = true;
// Fake userinfo endpoint to force v2 (cf. https://www.drupal.org/project/openid_connect_windows_aad/issues/3021812).
$config['openid_connect.settings.windows_aad']['settings']['userinfo_endpoint_wa'] = 'use_v2';
```

@todo Document default role mapping

## SAML

The [SAML Authentication](https://www.drupal.org/project/samlauth) module is
used for SAML authentication (!).

```php
// web/sites/*/settings.local.php
$config['samlauth.authentication']['sp_entity_id'] = 'os2loop;
$config['samlauth.authentication']['wsp_name_id_format'] = '';
// Folder containing `certs` folder with files `sp.{crt,key}`.
$config['samlauth.authentication']['sp_cert_folder'] = __DIR__.'/../../..';
$config['samlauth.authentication']['idp_entity_id'] = …; // Get this from you IdP metadata.
$config['samlauth.authentication']['idp_single_sign_on_service'] = …; // Get this from you IdP metadata.
$config['samlauth.authentication']['idp_x509_certificate'] = …; // Get this from you IdP metadata.
$config['samlauth.authentication']['unique_id_attribute'] = 'upn';
$config['samlauth.authentication']['create_users'] = TRUE;
$config['samlauth.authentication']['user_name_attribute'] = 'name';
$config['samlauth.authentication']['user_mail_attribute'] = 'mail';
$config['samlauth.authentication']['sp_name_id_format'] = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
```
