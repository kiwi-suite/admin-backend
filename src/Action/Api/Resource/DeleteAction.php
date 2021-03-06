<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Admin\Action\Api\Resource;

use Ixocreate\Admin\Entity\User;
use Ixocreate\Admin\Permission\Permission;
use Ixocreate\Admin\Resource\Action\DeleteActionAwareInterface;
use Ixocreate\Admin\Response\ApiErrorResponse;
use Ixocreate\Admin\Response\ApiSuccessResponse;
use Ixocreate\Application\Http\Middleware\MiddlewareSubManager;
use Ixocreate\Database\Repository\Factory\RepositorySubManager;
use Ixocreate\Database\Repository\RepositoryInterface;
use Ixocreate\Entity\EntityInterface;
use Ixocreate\Resource\ResourceInterface;
use Ixocreate\Resource\ResourceSubManager;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DeleteAction implements MiddlewareInterface
{
    /**
     * @var RepositorySubManager
     */
    private $repositorySubManager;

    /**
     * @var MiddlewareSubManager
     */
    private $middlewareSubManager;

    /**
     * @var ResourceSubManager
     */
    private $resourceSubManager;

    public function __construct(
        RepositorySubManager $repositorySubManager,
        MiddlewareSubManager $middlewareSubManager,
        ResourceSubManager $resourceSubManager
    ) {
        $this->repositorySubManager = $repositorySubManager;
        $this->middlewareSubManager = $middlewareSubManager;
        $this->resourceSubManager = $resourceSubManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $resource = $this->resourceSubManager->get($request->getAttribute('resource'));

        /** @var Permission $permission */
        $permission = $request->getAttribute(Permission::class);
        if (!$permission->can('resource.' . $resource->serviceName() . '.delete')) {
            return new ApiErrorResponse('forbidden', [], 403);
        }

        $middlewarePipe = new MiddlewarePipe();

        if ($resource instanceof DeleteActionAwareInterface) {
            /** @var MiddlewareInterface $action */
            $action = $this->middlewareSubManager->get($resource->deleteAction($request->getAttribute(User::class)));
            $middlewarePipe->pipe($action);
        }

        $middlewarePipe->pipe(new CallableMiddlewareDecorator(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($resource) {
            return $this->handleRequest($resource, $request, $handler);
        }));

        return $middlewarePipe->process($request, $handler);
    }

    private function handleRequest(ResourceInterface $resource, ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->repositorySubManager->get($resource->repository());

        /** @var EntityInterface $entity */
        $entity = $repository->find($request->getAttribute('id'));

        if (\method_exists($entity, 'deletedAt')) {
            //if(!$entity->deletedAt()) {
            $repository->save($entity->with('deletedAt', new \DateTimeImmutable()));
        /**
         * TODO: implement permanent deletion
         */
            //} else {
            //$repository->remove($entity);
            //}
        } else {
            $repository->remove($entity);
        }

        return new ApiSuccessResponse();
    }
}
