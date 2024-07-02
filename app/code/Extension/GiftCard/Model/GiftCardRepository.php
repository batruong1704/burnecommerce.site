<?php

namespace Extension\GiftCard\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Extension\GiftCard\Api\Data\GiftCardInterface;
use Extension\GiftCard\Api\GiftCardRepositoryInterface;
use Extension\GiftCard\Model\ResourceModel\GiftCard as GiftCardResource;
use Throwable;

/**
 * GiftCard repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftCardRepository implements GiftCardRepositoryInterface
{
    /**
     * @var GiftCardResource
     */
    private $giftCardResource;

    /**
     * @var GiftCardFactory
     */
    private $giftCardFactory;

    /**
     * @param GiftCardResource $giftCardResource
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        GiftCardResource $giftCardResource,
        GiftCardFactory  $giftCardFactory,
    )
    {
        $this->giftCardResource = $giftCardResource;
        $this->giftCardFactory = $giftCardFactory;
    }

    /**
     * @param GiftCardInterface $giftCard
     * @return GiftCardInterface
     * @throws CouldNotSaveException
     */
    public function save(GiftCardInterface $giftCard): GiftCardInterface
    {
        try {
            $this->giftCardResource->save($giftCard);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the gift card: %1', $exception->getMessage()),
                $exception
            );
        } catch (Throwable $exception) {
            throw new CouldNotSaveException(
                __('Could not save the gift card: %1', __('Something went wrong while saving the gift card.')),
                $exception
            );
        }

        return $giftCard;
    }

    /**
     * @param int $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function deleteById($id): bool
    {
        $giftCard = $this->getById($id);

        return $this->delete($giftCard);
    }

    /**
     * @param int $id
     * @return GiftCard
     * @throws NoSuchEntityException
     */
    public function getById($id): GiftCard
    {
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $id);
        if (!$giftCard->getId()) {
            throw new NoSuchEntityException(__('Unable to find giftCard with ID "%1"', $id));
        }

        return $giftCard;
    }

    /**
     * @param GiftCardInterface $giftCard
     * @return bool
     * @throws Exception
     */
    public function delete(GiftCardInterface $giftCard): bool
    {
        try {
            $this->giftCardResource->delete($giftCard);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the gift card: %1', $exception->getMessage())
            );
        }

        return true;
    }

    /**
     * @param string $code
     * @return GiftCardInterface
     * @throws NoSuchEntityException
     */
    public function getByCode($code): GiftCardInterface
    {
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $code, GiftCardInterface::CODE);
        if (!$giftCard->getId()) {
            throw new NoSuchEntityException(__('Unable to find giftCard with CODE "%1"', $code));
        }

        return $giftCard;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList() method.
    }
}
