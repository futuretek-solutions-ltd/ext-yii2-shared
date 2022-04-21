<?php

namespace futuretek\shared;

class Date extends \DateTime
{
    /**
     * Create Date object for specified date
     *
     * @param self|\DateTime|int|string $dateTime Date in valid format or DateTime object or timestamp(int)
     * @return self
     * @throws \Exception
     */
    public static function c($dateTime = 'now'): self
    {
        if ($dateTime instanceof self) {
            return clone $dateTime;
        }
        if ($dateTime instanceof \DateTime) {
            return new self($dateTime->format('c'));
        }
        if (is_int($dateTime)) {
            return new self('@' . $dateTime);
        }

        return new self($dateTime);
    }
}
