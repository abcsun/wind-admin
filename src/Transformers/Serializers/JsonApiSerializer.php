<?php

namespace Wind\Transformers\Serializers;

use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 本项目中API返回结构
 * {
 *     'code': $code
 *     'result': $data
 *     'message': $msg
 *     'pagination': 分页参数
 * }
 * 如果当期page超过最多分页数，抛出404异常.
 */
class JsonApiSerializer extends SerializerAbstract
{
    /**
     * 提示消息.
     *
     * @var [type]
     */
    public $msg;
    /**
     * API响应code，正常为1.
     *
     * @var [type]
     */
    public $code;

    public function __construct($code = 1, $msg = 'msg')
    {
        $this->msg = $msg;
        $this->code = $code;
    }
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        $resource = [
            'code' => $this->code,
            'result' => $data,
            'message' => $this->msg,
        ];

        return $resource;
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        $resource = [
            'code' => $this->code,
            'result' => $data,
            'message' => $this->msg,
        ];

        return $resource;
    }

    /**
     * Serialize the included data.
     *
     * @param ResourceInterface $resource
     * @param array             $data
     *
     * @return array
     */
    public function includedData(ResourceInterface $resource, array $data)
    {
        return $data;
    }

    /**
     * Serialize the meta.
     *
     * @param array $meta
     *
     * @return array
     */
    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }

        return $meta;
        // return ['meta' => $meta];
    }

    /**
     * 格式化分页信息.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $current_page = (int) $paginator->getCurrentPage();
        $total_page = (int) $paginator->getLastPage();

        if ($total_page && ($current_page > $total_page)) { //确保total_page大于0
            throw new NotFoundHttpException('请求页面不存在');
        }

        $pagination = [
            'total' => (int) $paginator->getTotal(),
            // 'count' => (int) $paginator->getCount(),
            'per_page' => (int) $paginator->getPerPage(),
            'current_page' => $current_page,
            'total_page' => $total_page,
        ];

        return ['pagination' => $pagination];
    }

    /**
     * Serialize the cursor.
     *
     * @param CursorInterface $cursor
     *
     * @return array
     */
    public function cursor(CursorInterface $cursor)
    {
    }

    /**
     * abstract method in League\\Fractal\\Serializer\\SerializerAbstract::null
     * 
     * @return [type] [description]
     */
    public function null()
    {
    }
}
