<?php

namespace Admin\NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Admin\NotificationBundle\Entity\NotificationLog;
use Admin\NotificationBundle\RecipientInterface;

/**
 * NotificationLogRepository
 **/
class NotificationLogRepository extends EntityRepository
{
    public function getUnreadCountByRecipient(RecipientInterface $recipient)
    {
        $dql = <<<EOQ
SELECT COUNT(1) FROM AdminNotificationBundle:NotificationLog l
WHERE l.recipientId = :recipientId
AND l.status = :status
EOQ;
        $query = $this->getEntityManager()
               ->createQuery($dql);
        $query->setParameter('recipientId', $recipient->getId());
        $query->setParameter('status', NotificationLog::STATUS_UNREAD);

        return $query->getSingleScalarResult();
    }

    public function findUnreadByRecipient(RecipientInterface $recipient)
    {
        $dql = <<<EOQ
SELECT l FROM AdminNotificationBundle:NotificationLog l
WHERE l.recipientId = :recipientId
AND l.status = :status
ORDER BY l.createdAt DESC
EOQ;
        $query = $this->getEntityManager()
               ->createQuery($dql);
        $query->setParameter('recipientId', $recipient->getId());
        $query->setParameter('status', NotificationLog::STATUS_UNREAD);

        return $query->getResult();
    }

    public function markAllReadByRecipient(RecipientInterface $recipient)
    {
        $dql = <<<EOQ
UPDATE AdminNotificationBundle:NotificationLog l
SET l.status = :status
WHERE l.recipientId = :recipientId
EOQ;
        $query = $this->getEntityManager()
               ->createQuery($dql);
        $query->setParameter('status', NotificationLog::STATUS_READ);
        $query->setParameter('recipientId', $recipient->getId());

        return $query->getResult();
    }
}
