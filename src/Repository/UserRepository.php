<?php
namespace KiwiSuite\Admin\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use KiwiSuite\Admin\Entity\User;
use KiwiSuite\CommonTypes\Entity\DateTimeType;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\Database\Repository\AbstractRepository;

final class UserRepository extends AbstractRepository
{

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return User::class;
    }

    public function loadMetadata(ClassMetadataBuilder $metadata): void
    {
        $metadata->setTable('admin_user');

        $metadata->createField('id', UuidType::class)
            ->makePrimaryKey()
            ->build();

        $metadata->addField('email', EmailType::class);
        $metadata->addField('password', Type::STRING);
        $metadata->addField('hash', UuidType::class);
        $metadata->addField('role', Type::STRING);
        $metadata->addField('avatar', Type::TEXT);
        $metadata->addField('createdAt', DateTimeType::class);
        $metadata->addField('lastLoginAt', DateTimeType::class);
    }
}