<?php declare(strict_types=1);

namespace MateuszMesek\AvoidRenameTableOnCatalogRuleIndexer\Plugin;

use Magento\CatalogRule\Model\Indexer\IndexerTableSwapper;
use MateuszMesek\DatabaseDataTransfer\Api\Command\TransferDataInterface;
use MateuszMesek\DatabaseDataTransfer\Api\Data\TableFactoryInterface;

class OnIndexerTableSwapper
{
    public function __construct(
        private readonly TableFactoryInterface $tableFactory,
        private readonly TransferDataInterface $transferData
    )
    {
    }

    public function aroundSwapIndexTables(
        IndexerTableSwapper $indexerTableSwapper,
        callable $proceed,
        array $originalTablesNames
    ): void
    {
        foreach ($originalTablesNames as $originalTableName) {
            $temporaryTableName = $indexerTableSwapper->getWorkingTableName($originalTableName);

            $this->transferData->execute(
                $this->tableFactory->create($originalTableName),
                $this->tableFactory->create($temporaryTableName)
            );
        }
    }
}
