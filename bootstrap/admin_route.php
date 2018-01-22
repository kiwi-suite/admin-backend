<?php
declare(strict_types=1);

namespace KiwiSuite\Admin;

/** @var \KiwiSuite\ApplicationHttp\Route\RouteConfigurator $adminRouteConfigurator */
use KiwiSuite\Admin\Action\Api\Auth\LoginAction;
use KiwiSuite\Admin\Action\Api\Auth\LogoutAction;
use KiwiSuite\Admin\Action\Api\Auth\UserAction;
use KiwiSuite\Admin\Action\Api\Config\ConfigAction;
use KiwiSuite\Admin\Middleware\Api\AuthorizationGuardMiddleware;
use KiwiSuite\Admin\Middleware\CookieInitializerMiddleware;

$adminRouteConfigurator->addGet('/api/config', ConfigAction::class, "config");
$adminRouteConfigurator->addPost('/api/auth/login', LoginAction::class, "auth.login");
$adminRouteConfigurator->addGet('/api/auth/user', UserAction::class, "auth.user", [AuthorizationGuardMiddleware::class]);
$adminRouteConfigurator->addPost('/api/auth/logout', LogoutAction::class, "auth.logout", [AuthorizationGuardMiddleware::class]);

$adminRouteConfigurator->addGet('[/[{path}]]', \KiwiSuite\Admin\Action\IndexAction::class, "admin", [CookieInitializerMiddleware::class]);

