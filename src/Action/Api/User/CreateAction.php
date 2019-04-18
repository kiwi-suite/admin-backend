<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Admin\Action\Api\User;

use Ixocreate\Package\Admin\Command\User\CreateUserCommand;
use Ixocreate\Package\Admin\Response\ApiErrorResponse;
use Ixocreate\Package\Admin\Response\ApiSuccessResponse;
use Ixocreate\CommandBus\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CreateAction implements MiddlewareInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = $request->getParsedBody();

        $createCommand = $this->commandBus->create(CreateUserCommand::class, $data);
        $commandResult = $this->commandBus->dispatch($createCommand);

        if (!$commandResult->isSuccessful()) {
            return new ApiErrorResponse('admin_create_user', $commandResult->messages());
        }

        return new ApiSuccessResponse(['id' => $createCommand->uuid()]);
    }
}
