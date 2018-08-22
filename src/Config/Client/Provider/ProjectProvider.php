<?php
/**
 * kiwi-suite/admin (https://github.com/kiwi-suite/admin)
 *
 * @package kiwi-suite/admin
 * @link https://github.com/kiwi-suite/admin
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace KiwiSuite\Admin\Config\Client\Provider;

use KiwiSuite\Admin\Config\AdminConfig;
use KiwiSuite\Contract\Admin\ClientConfigProviderInterface;
use KiwiSuite\Contract\Admin\RoleInterface;

final class ProjectProvider implements ClientConfigProviderInterface
{

    /**
     * @var AdminConfig
     */
    private $adminConfig;

    public function __construct(AdminConfig $adminConfig)
    {
        $this->adminConfig = $adminConfig;
    }

    /**
     * @param RoleInterface|null $role
     * @return array
     */
    public function clientConfig(?RoleInterface $role = null): array
    {
        return [
            'author' => $this->adminConfig->author(),
            'name' => $this->adminConfig->name(),
            'poweredBy' => $this->adminConfig->poweredBy(),
            'copyright' => $this->adminConfig->copyright(),
            'description' => $this->adminConfig->description(),
            'background' => $this->adminConfig->background(),
            'icon' => $this->adminConfig->icon(),
            'logo' => $this->adminConfig->logo(),
        ];
    }

    public static function serviceName(): string
    {
        return 'project';
    }
}