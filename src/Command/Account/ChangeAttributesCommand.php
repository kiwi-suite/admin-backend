<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Admin\Command\Account;

use Ixocreate\Package\Admin\Config\AdminConfig;
use Ixocreate\Package\Admin\Entity\User;
use Ixocreate\Package\Admin\Repository\UserRepository;
use Ixocreate\Package\CommandBus\Command\AbstractCommand;
use Ixocreate\Package\Type\Entity\SchemaType;
use Ixocreate\Package\Schema\AdditionalSchemaInterface;
use Ixocreate\Package\Entity\Type\Type;
use Ixocreate\Package\Schema\AdditionalSchema\AdditionalSchemaSubManager;

class ChangeAttributesCommand extends AbstractCommand
{
    /**
     * @var AdminConfig
     */
    private $adminConfig;

    /**
     * @var AdditionalSchemaSubManager
     */
    private $additionalSchemaSubManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ChangeAttributesCommand constructor.
     * @param AdminConfig $adminConfig
     * @param AdditionalSchemaSubManager $additionalSchemaSubManager
     * @param UserRepository $userRepository
     */
    public function __construct(AdminConfig $adminConfig, AdditionalSchemaSubManager $additionalSchemaSubManager, UserRepository $userRepository)
    {
        $this->adminConfig = $adminConfig;
        $this->additionalSchemaSubManager = $additionalSchemaSubManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws \Exception
     * @return bool
     */
    public function execute(): bool
    {
        $data = $this->data();

        /** @var User $user */
        $user = $this->userRepository->find($data['userId']);

        $additionalSchema = $this->receiveAdditionalSchema();

        if ($additionalSchema !== null) {
            $type = Type::create($data['data'], SchemaType::class, [
                'provider' => ['class' => AdditionalSchemaSubManager::class, 'name' => $additionalSchema::serviceName()],
            ]);

            $user = $user->with('accountAttributes', $type);
            $user = $user->with('updatedAt', new \DateTimeImmutable());
        }

        $this->userRepository->save($user);

        return true;
    }

    public static function serviceName(): string
    {
        return "admin.account-change-attributes";
    }

    /**
     * @return AdditionalSchemaInterface|null
     */
    private function receiveAdditionalSchema(): ?AdditionalSchemaInterface
    {
        $schema = null;

        if (!empty($this->adminConfig->accountAttributesSchema())) {
            $schema = $this->additionalSchemaSubManager->get($this->adminConfig->accountAttributesSchema());
        }

        return $schema;
    }
}
