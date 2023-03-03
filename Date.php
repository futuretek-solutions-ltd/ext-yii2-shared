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

    /**
     * Compare two dates.
     * Return:
     * <ul>
     * <li>-1 if first <i>d1</i> is less than <i>d2</i></li>
     * <li>1 if <i>d1</i> is greater than <i>d2</i></li>
     * <li>0 if they are equal</li>
     * </ul>
     *
     * @param \DateTime|int|string $d1 Date in valid format or DateTime object or timestamp(int)
     * @param \DateTime|int|string $d2 Date in valid format or DateTime object or timestamp(int)
     * @return int
     * @throws \Exception
     */
    public static function compare($d1, $d2): int
    {
        return strcmp(self::c($d1)->format('Y-m-d'), self::c($d2)->format('Y-m-d'));
    }
}
