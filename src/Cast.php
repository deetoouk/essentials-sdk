<?php

namespace JTDSoft\EssentialsSdk;

use JTDSoft\EssentialsSdk\Exceptions\ErrorException;

/**
 * Class Cast
 *
 * @package JTDSoft\EssentialsSdk
 */
class Cast
{
    /**
     * @param Object $cast
     * @param        $response
     *
     * @return Object
     * @internal param string $cast
     */
    public static function single(Object $cast, $response)
    {
        $cast_class = get_class($cast);

        $new = new $cast_class($response, true);

        $new->expand($cast->getExpand());
        $new->options($cast->getOptions());

        return $new;
    }

    /**
     * @param Object $cast
     * @param      $response
     *
     * @return mixed
     * @throws ErrorException
     */
    public static function many(Object $cast, $response): iterable
    {
        $result = new Collection();

        if (!$response) {
            return $result;
        }

        if (isset($response['total']) && isset($response['data'])) { //for paging
            $paging = new Paging(static::many($cast, $response['data']));

            $paging->setPage($response['current_page']);
            $paging->setTotal($response['total']);
            $paging->setLastPage($response['last_page']);
            $paging->setFrom($response['from']);
            $paging->setTo($response['to']);

            return $paging;
        }

        foreach ($response as $key => $value) {
            $result->push(static::single($cast, $value));
        }

        return $result;
    }
}
