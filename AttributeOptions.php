<?php
namespace Wheelpros\CheckoutExtended\Model\Config;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class AttributeOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ProductCollectionFactory
     */
    protected ProductCollectionFactory $productCollectionFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        // Add SKU attribute option with actual values
        $skuValues = $this->getProductAttributeValues('sku');
        $options[] = [
            'label' => __('SKU'),
            'value' => 'sku',
            'values' => $skuValues,
        ];

        // Add Name attribute option with actual values
        $nameValues = $this->getProductAttributeValues('name');
        $options[] = [
            'label' => __('Name'),
            'value' => 'name',
            'values' => $nameValues,
        ];

        // Add Qty attribute option with actual values
        $qtyValues = $this->getProductAttributeValues('qty');
        $options[] = [
            'label' => __('Qty'),
            'value' => 'qty',
            'values' => $qtyValues,
        ];

        // Add Price attribute option with actual values
        $priceValues = $this->getProductAttributeValues('price');
        $options[] = [
            'label' => __('Price'),
            'value' => 'price',
            'values' => $priceValues,
        ];

        // Add Image attribute option with actual values
        $imageValues = $this->getProductAttributeValues('image');
        $options[] = [
            'label' => __('Image'),
            'value' => 'image',
            'values' => $imageValues,
        ];

        return $options;
    }

    /**
     * @param $attributeCode
     * @return array
     */
    protected function getProductAttributeValues($attributeCode): array
    {
        $values = [];

        // Create a product collection
        $collection = $this->productCollectionFactory->create();

        // Apply filters to the collection
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);

        // Add filters to load specific attributes
        $collection->addAttributeToSelect([$attributeCode, 'sku', 'name', 'qty', 'price', 'image']);

        // Set pagination parameters
        $pageSize = 100; // Adjust the number of products per page if needed
        $currentPage = 1; // Adjust the current page if needed

        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);

        // Retrieve all products matching the filters
        $products = $collection->getItems();

        foreach ($products as $product) {
            // Retrieve the value of the specified attribute for each product
            $attributeValue = $product->getData($attributeCode);

            // Add the attribute value to the values array
            if ($attributeValue !== null) {
                $values[$attributeValue] = $attributeValue;
            }
        }

        return $values;
    }



}
