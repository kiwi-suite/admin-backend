<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Admin\Resource\Action;

use Ixocreate\Admin\UserInterface;

interface DeleteActionAwareInterface
{
    public function deleteAction(UserInterface $user): string;
}
