<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Handler\Gallery;

use Migration\Handler\AbstractHandler;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\Record;
use Migration\Config;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class InsertValueToEntity
 */
class InsertValueToEntity extends AbstractHandler
{
    /**
     * @var Destination
     */
    protected $destination;

    /**
     * @var string
     */
    protected $valueToEntityDocument = 'catalog_product_entity_media_gallery_value_to_entity';

    /**
     * @var string
     */
    protected $entityField = 'entity_id';

    /**
     * @var string
     */
    protected $editionMigrate = '';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param Destination $destination
     * @param Config $config
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Destination $destination,
        Config $config,
        ModuleListInterface $moduleList
    ) {
        $this->destination = $destination;
        $this->editionMigrate = $config->getOption('edition_migrate');
        $this->moduleList = $moduleList;
    }

    /**
     * @inheritdoc
     */
    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($recordToHandle);
        $entityIdName = $this->editionMigrate == Config::EDITION_MIGRATE_OPENSOURCE_TO_OPENSOURCE
            || $this->moduleList->has('Magento_CatalogStaging') === false
            ? $this->entityField
            : 'row_id';
        $valueId = (int)$recordToHandle->getValue($this->field);
        $entityId = (int)$recordToHandle->getValue($this->entityField);

        $tableName = $this->destination->addDocumentPrefix($this->valueToEntityDocument);
        $existing = $this->destination->getAdapter()->loadPage(
            $tableName,
            0,
            1,
            null,
            null,
            new \Zend_Db_Expr(sprintf('`value_id` = %d AND `%s` = %d', $valueId, $entityIdName, $entityId))
        );
        if (!empty($existing)) {
            return;
        }

        $record['value_id'] = $valueId;
        $record[$entityIdName] = $entityId;
        $this->destination->saveRecords($this->valueToEntityDocument, [$record]);
    }
}
