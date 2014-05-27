<?php

namespace Api\Service\Transaction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Api\Service\Locale;
use Api\Entities\Transaction;
use Api\Service\AccessorTrait;

/**
 * Class Transactions
 *
 * @package Api\Service\Transaction
 *
 * @method Data             setTransactionEntity(EntityRepository $transactionEntity)
 * @method Data             setLocale(Locale $locale)
 * @method Data             setEntityManager(EntityManager $entityManager)
 * @method EntityRepository getTransactionEntity()
 * @method Locale           getLocale()
 * @method EntityManager    getEntityManager()
 */
class Data
{
    use AccessorTrait;

    /**
     * @return string
     */
    private function getBaseDql()
    {
        $dql = 'SELECT '
               . 't.id, t.date as dateTransaction, t.dateCreated, t.price as amount, '
               . 'c.currency, c.name as currencyName, c.html as currencySymbol, '
               . 'u.email, u.role, u.id as userId, u.locale, u.timezone, u.displayName as userName, '
               . 'i.name as itemName, i.id as itemId, '
               . 'g.name as groupName, i.id as groupId '
               . 'FROM \Api\Entities\Transaction t '
               . 'JOIN t.currency c '
               . 'JOIN t.user u '
               . 'JOIN t.item i '
               . 'JOIN t.group g';

        return $dql;
    }

    /**
     * @param array          $userIds
     * @param int            $offset
     * @param int            $limit
     * @param \DateTime|null $dateFrom
     * @param \DateTime|null $dateTill
     * @param string         $item
     * @param string         $group
     * @param string         $price
     *
     * @return array
     */
    public function getTransactionsList(
        array $userIds,
        $offset,
        $limit,
        \DateTime $dateFrom = null,
        \DateTime $dateTill = null,
        $item,
        $group,
        $price
    ) {
        $dql = $this->getBaseDql();
        $dql .= ' WHERE u.id IN (:userIds)';

        $parameters = array('userIds' => $userIds);

        if (!empty($dateFrom)) {
            $dql .= ' AND t.date >= :dateFrom';
            $parameters['dateFrom'] = $dateFrom->format('Y-m-d');
        }

        if (!empty($dateTill)) {
            $dql .= ' AND t.date <= :dateTill';
            $parameters['dateTill'] = $dateTill->format('Y-m-d');
        }

        if (!empty($item)) {
            $dql .= ' AND i.name LIKE :item';
            $parameters['item'] = $item;
        }

        if (!empty($group)) {
            $dql .= ' AND g.name LIKE :group';
            $parameters['group'] = $group;
        }

        if (!empty($price)) {
            $dql .= ' AND t.price LIKE :price';
            $parameters['price'] = $price;
        }

        $dql .= ' ORDER BY t.id DESC';

        $transactions = $this->fetchResults($dql, $parameters, $limit, $offset);

        return $transactions;
    }

    /**
     * @param string $dql
     * @param array  $parameters
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    private function fetchResults($dql, array $parameters, $limit, $offset)
    {
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);
        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getResult();
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    public function toArray(Transaction $transaction)
    {
        $data = array(
            'id'              => $transaction->getId(),
            'dateTransaction' => $transaction->getDate(),
            'dateCreated'     => $transaction->getDateCreated(),
            'amount'          => $transaction->getPrice(),
            'currency'        => $transaction->getCurrency()->getCurrency(),
            'currencyName'    => $transaction->getCurrency()->getName(),
            'currencySymbol'  => $transaction->getCurrency()->getHtml(),
            'email'           => $transaction->getUser()->getEmail(),
            'role'            => $transaction->getUser()->getRole(),
            'userId'          => $transaction->getUser()->getId(),
            'locale'          => $transaction->getUser()->getLocale(),
            'timezone'        => $transaction->getUser()->getTimezone(),
            'userName'        => $transaction->getUser()->getDisplayName(),
            'itemName'        => $transaction->getItem()->getName(),
            'itemId'          => $transaction->getItem()->getId(),
            'groupName'       => $transaction->getGroup()->getName(),
            'groupId'         => $transaction->getGroup()->getId(),
        );

        return $data;
    }

    /**
     * @param array $transactions
     *
     * @return array
     */
    public function normalizeResults(array $transactions)
    {
        foreach ($transactions as &$data) {
            foreach ($data as $key => &$value) {
                if ($key == 'dateTransaction') {
                    /** @var \DateTime $value */
                    $data['date'] = $value->format('Y-m-d');
                    $value = $this
                        ->getLocale()
                        ->setLocale($data['locale'])
                        ->setTimezone($data['timezone'])
                        ->getDateFormatter()
                        ->format($value);
                }

                if ($key == 'dateCreated') {
                    $data['created'] = $value->format('Y-m-d H:i:s');
                    $value = $this
                        ->getLocale()
                        ->setLocale($data['locale'])
                        ->setTimezone($data['timezone'])
                        ->getDateTimeFormatter()
                        ->format($value);
                }

                if ($key == 'amount') {
                    /** @var int $value */
                    $data['price'] = sprintf('%0.2f', $value / 100);
                    $data['money'] = $this
                        ->getLocale()
                        ->setLocale($data['locale'])
                        ->setTimezone($data['timezone'])
                        ->getFormattedMoney(
                            $data['currency'],
                            $value
                        );
                }
            }
        }

        return $transactions;
    }

    /**
     * @param int $transactionId
     *
     * @return Transaction
     */
    public function find($transactionId)
    {
        return $this->getTransactionEntity()->find($transactionId);
    }
}
