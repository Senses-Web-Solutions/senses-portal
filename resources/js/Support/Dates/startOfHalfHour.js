import { millisecondsInMinute, startOfHour, toDate } from 'date-fns';

/**
 * @name startOfHalfHour
 * @category Hour Helpers
 * @summary Return the start of a half hour for the given date.
 *
 * @description
 * Return the start of an hour for the given date.
 * The result will be in the local timezone.
 *
 * @param date - the original date
 * @returns the start of an hour
 *
 * @example
 * // The start of an hour for 2 September 2014 11:55:00:
 * const result = startOfHalfHour(new Date(2014, 8, 2, 11, 55))
 * //=> Tue Sep 02 2014 11:30:00
 */
export default function startOfHalfHour(dirtyDate) {
    const date = toDate(dirtyDate);
    const dateStartOfHour = startOfHour(date);

    const diff = date - dateStartOfHour;

    // If the difference is more than 30 minutes
    if (diff > (millisecondsInMinute * 30)) {
        return date.setMinutes(30, 0, 0);
    }

    return dateStartOfHour;
}
