<?php

use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Http\Response;
use Wind\Models\ConfigModel;

/*
 * 全局函数文件
 * 在bootstrap中作为首个文件加载
 * @author scl <scl@winhu.com>
 */

if (!function_exists('generate_payment_num')) {
    /**
   * 生成支付单号, 如T20160101888.
   */
  function generate_payment_num()
  {
      return 'T'.date('YmdHis').rand(100, 999);
  }
}

if (!function_exists('generate_student_id')) {
    /**
     * 生成学号，学号由10位组成。x开头为正常学号，s开头表示出现学号生成竞争失败的学生。
     * TODO：生成s学号需要及时发出系统运维通知.
     */
    function generate_student_id()
    {
        $redis = Redis::connection('default');

        //获取互斥锁
        $i = 0;
        while (null === ($result = $redis->LPOP('important:student_id_mutex'))) {
            if ($i++ > 3) { //多次尝试获取失败后生成s开头学号
                $id = $redis->GET('important:student_id');

                return 's'.str_pad($id, 9, '0', STR_PAD_LEFT);
            }
            usleep(100000); //100ms
        }

        //共享资源并发部分
        $id = $redis->GET('important:student_id');
        $redis->INCR('important:student_id');

        //互斥锁释放
        $redis->LPUSH('important:student_id_mutex', 1);

        return 'x'.str_pad($id, 9, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('parse_ids_from_str')) {
    /**
     * 从字符串解析出id数组。多用于批量操作时携带的资源id.
     *
     * @param [type] $str [description]
     *
     * @return array [description]
     */
    function parse_ids_from_str($str)
    {
        preg_match_all("/\d+/", $str, $matchs);

        return isset($matchs[0]) ? $matchs[0] : array();
    }
}

if (!function_exists('response_json')) {
    /**
     * 按照固定格式返回json.
     */
    function response_json($code = 1, $result = '', $message = '', $status = 200, $pagination = [])
    {
        $content = compact('code', 'result', 'message', 'pagination');

        return (new Response($content, $status))->header('Content-Type', 'application/json');
    }
}

if (!function_exists('parse_config_value')) {
    /**
     * 根据配置类型type解析出正确的值value
     * type类型为数组时(3),value分割成数组.
     *
     * @param $type
     * @param $value
     *
     * @return array
     */
    function parse_config_value($type, $value)
    {
        switch (intval($type)) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\s\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }

        return $value;
    }
}

if (!function_exists('get_config_from_cache')) {
    /**
     * 从缓存中获取key的值。不存在时返回false.
     * 
     * @param [type] $index [description]
     *
     * @return [type] [description]
     */
    function get_config_from_cache($index = null)
    {
        $config = Cache::tags('system')->get('CONFIG_CACHE');

        return isset($config[$index]) ? $config[$index] : false;
    }
}

if (!function_exists('load_config_to_cache')) {
    /**
     * 从数据库中加载配置到内存中.
     *
     * @return [type] [description]
     */
    function load_config_to_cache()
    {
        $key = 'CONFIG_CACHE';
        if (Cache::tags('system')->has($key)) {
            return;
        }

        $configs = ConfigModel::all(['name', 'type', 'value']);
        $data = array();
        foreach ($configs as $config) {
            $data[$config->name] = $config->value;
        }

        Cache::tags('system')->forever('CONFIG_CACHE', $data);
    }
}

if (!function_exists('reload_config_cache')) {
    /**
     * 更新缓存中的配置信息.
     *
     * @param $type
     * @param array $input
     */
    function reload_config_cache($type, array $input)
    {
        $config = Cache::tags('system')->get('CONFIG_CACHE');

        switch ($type) {
            case 1: //更新缓存数据
                unset($config[$input['old_name']]);
                $name = strtoupper($input['name']);
                $config[$name] = $input['value'];
                Cache::tags('system')->forever('CONFIG_CACHE', $config);
                break;
            case 2: //新增缓存数据
                $name = strtoupper($input['name']);
                $config[$name] = $input['value'];
                Cache::tags('system')->forever('CONFIG_CACHE', $config);
                break;
            case 3: //删除缓存数据
                $name = $input['name'];
                unset($config[$name]);
                Cache::tags('system')->forever('CONFIG_CACHE', $config);
                break;
            default:
                break;
        }
    }
}

if (!function_exists('C')) {
    /**
     * get_config_from_cache的别名.
     *
     * @param [type] $key [description]
     */
    function C($key)
    {
        return get_config_from_cache($key);
    }
}

if (!function_exists('generate_jwt_token')) {
    /**
     * 生成新的JWT token.
     */
    function generate_jwt_token($user_id, $token_ttl = 3600)
    {
        //生成token
        $claims = [
            'sub' => $user_id,
            'exp' => time() + $token_ttl,
        ];
        $payload = JWTFactory::make($claims);
        $token = JWTAuth::encode($payload)->get();

        return $token;

        // $user_repo = App::make('user_repository');
        // $info = $user_repo->getUserInfo($user_id);
        // cache_user_token($user_id, $token, $info); //缓存用户登录token及基本信息
        // $user_data = array(
        //     'token' => $token,
        //     'user_info' => $info, //获取个人基本信息,
        // );
        // return $user_data;
    }
}

if (!function_exists('generate_randomstr')) {
    /**
   * 生成随机字符串.
   */
  function generate_randomstr($length = 32)
  {
      $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
    $strlen = 62;
      while ($length > $strlen) {
          $str .= $str;
          $strlen += 62;
      }
      $str = str_shuffle($str);

      return substr($str, 0, $length);
  }
}

if (!function_exists('is_mobile')) {
    /**
     * 检测是否移动端请求
     *
     * @param [type] $userAgent [description]
     *
     * @return bool [description]
     */
    function is_mobile($userAgent)
    {
        $clientkeywords = array(
        'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
        'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini',
        'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match('/('.implode('|', $clientkeywords).')/i', $userAgent) && strpos($userAgent, 'ipad') === false) {
            return true;
        }

        return false;
    }
}

if (!function_exists('cache_user_token')) {
    /**
     * 缓存用户及token.
     *
     * @param [type] $user_id   [description]
     * @param [type] $token     [description]
     * @param [type] $user_info [description]
     *
     * @return [type] [description]
     */
    function cache_user_token($user_id, $token, $user_info)
    {
        //缓存用户登录token及基本信息
        $md5_token = md5($token);
        $key = Config('sys.USER_CACHE_PREFIX').$md5_token;  //为了减少key的长度，重新对token进行散列计算
        Cache::put($key, $user_info, Config('sys.USER_CACHE_TIME')); //token=>user_info

        $last_token_key = Config('sys.USER_LAST_TOKEN_CACHE_PREFIX').$user_id;
        Cache::put($last_token_key, $md5_token, 100); //user_id=>last_avail_token
    }
}

if (!function_exists('is_qiniu_callback')) {
    /**
     * 判断七牛的回调是否合法，防止其它恶意调用
     * <http://developer.qiniu.com/docs/v6/api/overview/up/upload-models/response-types.html#callback>
     * C('accessKey')取得 AccessKey
     * C('secretKey')取得 SecretKey  
     * callback.php 为回调地址的Path部分  
     * file_get_contents('php://input')获取RequestBody,其值形如:  
     * name=sunflower.jpg&hash=Fn6qeQi4VDLQ347NiRm-RlQx_4O2\
     * &location=Shanghai&price=1500.00&uid=123.
     */
    function is_qiniu_callback($authstr = '', $data = '')
    {
        $authstr = $_SERVER['HTTP_AUTHORIZATION'];
        $find = preg_match('/QBox /', $authstr);
        if (!$find) {
            return false;
        }
        $auth = explode(':', substr($authstr, 5));
        $access_key = Config('filesystems.disks.qiniu_private.access_key');
        $secret_key = Config('filesystems.disks.qiniu_private.secret_key');

        if ((sizeof($auth) != 2) || ($auth[0] != $access_key)) {
            return false;
        }
         //$data = "/".$path."\n".file_get_contents('php://input');
        // var_dump((hash_hmac('sha1',$data,Config('filesystems.disks.qiniu_private.secret_key'), true)));
//        var_dump(safe_base64_encode(hash_hmac('sha1', $data, $secret_key, true)));
//        var_dump($auth[1]);
        return safe_base64_encode(hash_hmac('sha1', $data, $secret_key, true)) == $auth[1];
    }
}

if (!function_exists('is_api_auth')) {
    /**
     * 判断公开的受限API调用是否合法，防止其它恶意调用，验证算法参考is_qiniu_callback
     * <http://developer.qiniu.com/docs/v6/api/overview/up/upload-models/response-types.html#callback>.
     * 
     * authstr是请求头的authorization携带的字符串，格式为 Auth accesskey:encode_data
     * 其中encode_data计算如safe_base64_encode(hash_hmac('sha1', $data, $secret_key, true))
     * 
     * data的值由回调地址和post的body拼接而成，形如"/".Request::path()."\n".file_get_contents('php://input');
     */
    function is_api_auth($authstr = '', $data = '')
    {
        // $authstr = $_SERVER['HTTP_AUTHORIZATION'];
        $find = preg_match('/Auth /', $authstr);
        if (!$find) {
            return false;
        }

        $auth = explode(':', substr($authstr, 5));
        $sys = get_config_from_cache();
        $access_key = isset($sys['API_ACCESS_KEY']) ? $sys['API_ACCESS_KEY'] : '';
        $secret_key = isset($sys['API_SECRET_KEY']) ? $sys['API_SECRET_KEY'] : '';

        if ((sizeof($auth) != 2) || ($auth[0] != $access_key)) {
            return false;
        }
        // var_dump(safe_base64_encode(hash_hmac('sha1', $data, $secret_key, true)));
        return safe_base64_encode(hash_hmac('sha1', $data, $secret_key, true)) == $auth[1];
    }
}

if (!function_exists('config_path')) {
    /**
     * 由于laravel中有此函数用于服务注册时，所以直接使用laravel的服务提供包会导致函数未定义错误
     * 因此定义该函数，用于规避该问题.
     *
     * @param $array1, $array2
     *
     * @return array
     *
     * @author scl@winhu.com
     */
    function config_path($path = '')
    {
    }
}

if (!function_exists('verify_code_with_phone')) {
    /**
     * 验证手机号与验证码是否匹配.
     *
     * @param $code, $phone
     *
     * @return bool
     *
     * @author scl@winhu.com
     */
    function verify_code_with_phone($code, $phone, $flush = true)
    {
        $prefix = Config('laravel-sms.VERIFY_CODE_CACHE_PREFIX');
        $key = "$prefix$phone.$code";
        if (Cache::has($key)) {
            if ($flush) {
                Cache::forget($key);
            }

            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('update_statistics_field')) {
    /**
     * 更新统计字段，主要用于model中的字段增减更新.
     *
     * @param [type] $model   [description]
     * @param [type] $id      [description]
     * @param [type] $field   [description]
     * @param [type] $count   [description]
     * @param string $primary [description]
     *
     * @return [type] [description]
     */
    function update_statistics_field($model, $id, $field, $count, $primary = 'id')
    {
        return $model::where('id', $id)->increment($field, $count);
    }
}

if (!function_exists('formate_page_data')) {
    /**
     * 格式化分页数据信息.
     *
     * @param [type] $pagination_data [description]
     *
     * @return [type] [description]
     */
    function formate_page_data($pagination_data)
    {

        //过滤有效参数
        $pagination = array(
            'total' => $pagination_data['total'],
            'per_page' => $pagination_data['per_page'],
            'current_page' => $pagination_data['current_page'],
            'next_page' => $pagination_data['current_page'] + 1,
            'last_page' => $pagination_data['last_page'],

        );

        return $pagination;
    }
}

if (!function_exists('safe_base64_encode')) {
    /**
     * 安全的base64编码
     *
     * @param $str
     *
     * @return mixed
     *
     * @author yangyifan <yangyifanphp@gmail.com>
     */
    function safe_base64_encode($str)
    {
        $find = array('+', '/');
        $replace = array('-', '_');

        return str_replace($find, $replace, base64_encode($str));
    }
}

if (!function_exists('safe_base64_decode')) {
    /**
     * 安全的base64解码
     *
     * @param $str
     *
     * @return mixed
     *
     * @author yangyifan <yangyifanphp@gmail.com>
     */
    function safe_base64_decode($str)
    {
        $find = array('-', '_');
        $replace = array('+', '/');

        return base64_decode(str_replace($find, $replace, $str));
    }
}

if (!function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree.
     *
     * @param array  $list  要转换的数据集
     * @param string $pid   parent标记字段
     * @param string $level level标记字段
     *
     * @return array
     */
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
    {
        //      dd($list);
      // 创建Tree
      $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                // $list[$key][$child] = array(); 直接给所有记录都增加$child字段，保证数据结构的一致
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    if (!isset($list[$key][$child])) {
                        $list[$key][$child] = array();  //或者只给顶级数据中均增加$child的key，即便其结果为空         
                    }
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }
}

if (!function_exists('formate_3level_area')) {
    /**
     * 格式化区域数据位3层结构，数据源来自winshop_area，市使用city，区县使用area.
     *
     * @param [type] $list [description]
     * @param string $pk   [description]
     * @param string $pid  [description]
     * @param int    $root [description]
     *
     * @return [type] [description]
     */
    function formate_3level_area($list, $pk = 'id', $pid = 'pid', $root = 0)
    {
        $tree = array();
        if (!is_array($list)) {
            return $tree;
        }

        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            // $list[$key][$child] = array(); 直接给所有记录都增加$child字段，保证数据结构的一致
            $refer[$data[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $value) {
            $parent_id = $value['pid'];
            if ($parent_id == 0) { //省
                $tree[] = &$list[$key];
            } elseif ($parent_id % 10000 == 0) { //市
                $parent = &$refer[$parent_id];
                $parent['city'][] = &$list[$key];
            } else { //区县
                $parent = &$refer[$parent_id];
                $parent['area'][] = &$list[$key];
            }
        }

        return $tree;
    }
}

if (!function_exists('str2arr')) {
    /**
    * 字符串转换为数组，主要用于把分隔符调整到第二个参数.
    *
    * @param  string $str  要分割的字符串
    * @param  string $glue 分割符
    *
    * @return array
    *
    * @author 麦当苗儿 <zuojiazi@vip.qq.com>
    */
   function str2arr($str, $glue = ',')
   {
       return explode($glue, $str);
   }
}

if (!function_exists('arr2str')) {
    /**
    * 数组转换为字符串，主要用于把分隔符调整到第二个参数.
    *
    * @param  array  $arr  要连接的数组
    * @param  string $glue 分割符
    *
    * @return string
    *
    * @author 麦当苗儿 <zuojiazi@vip.qq.com>
    */
   function arr2str($arr, $glue = ',')
   {
       return $arr ? implode($glue, $arr) : '';
   }
}

if (!function_exists('get_parse_model')) {
    /**
     * 截取revision 表中model 名称.
     *
     * @param $str
     *
     * @return string|void
     */
    function get_parse_model($str)
    {
        if (empty($str)) {
            return;
        }
        $model = substr($str, strripos($str, '\\'), strlen($str));
        $model = substr($model, 0, stripos($model, 'Model'));

        return trim($model, '\\*');
    }
}

if (!function_exists('return_revision_description')) {
    /**
     * 返回revision 描述信息.
     *
     * @param $key
     * @param $model
     * @param $id
     *
     * @return string
     */
    function return_revision_description($key, $model, $id, $value)
    {
        $desc = '';
        $model = get_parse_model($model);
        if ($key == 'deleted_at') {
            $desc .= '删除: '.$model.' ID为'.$id;
        } else {
            $desc .= '修改: #'.$id.' '.$model.' 中的 '.$key.' 为 '.$value;
        }

        return $desc;
    }
}

if (!function_exists('parse_str_array')) {
    /**
     * 将字符串分割成数组.
     *
     * @param $str
     * @param string $separator
     *
     * @return array|void
     */
    function parse_str_array($str, $separator = ',')
    {
        $return = array();
        if (trim($str) == '') {
            return $return;
        }

        $list = explode($separator, $str);
        foreach ($list as $v) {
            $return[] = trim($v);
        }

        return $return;
    }
}
