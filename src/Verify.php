<?php
namespace tool;



// Verify::instance()->verify([
//     'str.require'   =>  '失败',
//     'num.require'   =>  'asdasda',
//     'num.spot:4'    =>  '不是两位小数',
//     'a.gt:2'        =>  '要大于2',
//     'a.lt:num'      =>  '要小于4',
//     'str.sc'      =>  '包含特殊字符',
//     'abc.in:gg'        =>  '是否包含他',
//     'num.eq:4'    =>  '等于三',
//     'phone.phone'   =>  '哥们这是电话号吗，你的电话号长这样？？'
// ],['str'=>"2as阿斯顿",'num'   =>  4 ,'a'=>3,'abc'=>2,'gg'=>range(1,55),'phone'=>12323232323]);

/**
 * 基础字段验证器
 *
 * @author EricGU178
 */
class Verify extends Base
{
    /**
     * 没有扩展参数的数组
     *
     * @var array
     * @author EricGU178
     */
    protected $unRuleExt = ['require','integer','sc','phone','array'];

    /**
     * 必须有扩展参数的数组
     *
     * @var array
     * @author EricGU178
     */
    protected $ruleExt = ['in','notin','eq'];

    /**
     * 规则名
     *
     * @var array
     * @author EricGU178
     */
    protected $ruleNames = [
        'require'   =>  '验证非空',
        'integer'   =>  '验证整数',
        'sc'        =>  '验证包含特殊字符',
        'gt'        =>  '验证是否大于',
        'egt'       =>  '验证是否大于等于',
        'lt'        =>  '验证是否小于',
        'elt'       =>  '验证是否小于等于',
        'eq'        =>  '验证是否等于',
        'spot'      =>  '验证是否有小数点N位',
        'in'        =>  '验证是否包含',
        'notin'     =>  '验证是否不包含',
        'phone'     =>  '验证电话号码格式',
        'array'     =>  '验证是否是数组',
        'email'     =>  '验证是否符合邮箱'
    ];

    protected $data = [];

    // 初始化
    public function __construct()
    {
        // parent::__construct();
    }


    /**
     * 验证规则
     *
     * @param array $rule   规则
     * @param array $data   数据
     * @return string|void
     * @author EricGU178
     */
    public function verify($rule,$data)
    {
        if (!is_array($rule) || !is_array($data)) {
            throw new \Exception('规则必须是数组或者数据必须是数组');
        }
        $this->data = $data;
        foreach ($rule as $key => $value) {

            $_front = [];
            $pre_front = explode('.' , $key);

            if ($this->hasField($pre_front[0])) {
                throw new \Exception("「{$pre_front[0]}」在数据中字段不存在");
            }

            if (strpos($pre_front[1],':') !== false ) {
                $_front = explode(':',$pre_front[1]);
                $_front = array_merge([$pre_front[0]],$_front);
            } else {
                $_front = $pre_front;
            }

            if ($this->hasRule($_front[1])) {
                throw new \Exception("「{$_front[1]}」规则不存在");
            }
            
            if (count($_front) < 2 || count($_front) > 3) {
                throw new \Exception("{$key}格式不正确,验证字段.验证规则:字段或者具体的数字");
            }

            if ($value == "") {
                throw new \Exception("请填写{$_front[0]}的提示信息");
            }

            if (in_array($_front[1],$this->unRuleExt) && isset($_front[2])) {
                throw new \Exception("「{$this->ruleNames[$_front[1]]}」没有扩展字段");
            }

            if (in_array($_front[1],$this->ruleExt) && !isset($_front[2])) {
                throw new \Exception("「{$this->ruleNames[$_front[1]]}」必须拥有扩展字段");
            }

            try {
                if ($this->distribute($_front)) {
                    throw new \Exception($value);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }

        }
    }

    /**
     * 检测规则名涉及到的字段在数据中存在与否
     *
     * @return void
     * @author EricGU178
     */
    private function hasField($field)
    {
        return isset($this->data[$field]) ? false : true;
    }

    /**
     * 检测规则名是否存在
     *
     * @return void
     * @author EricGU178
     */
    private function hasRule($rule_name)
    {
        return isset($this->ruleNames[$rule_name]) ? false : true;
    }

    /**
     * 验证方法分发
     *
     * @param array $verify_rule
     * @param string $message
     * @param array $data
     * @return void
     * @author EricGU178
     */
    private function distribute($verify_rule) {
        switch ($verify_rule[1]) {
            case 'require':
                return $this->require($verify_rule[0]);
                break;
            case 'integer':
                return $this->verifyInteger($verify_rule[0]);
                break;
            case 'gt':
                return $this->gt($verify_rule[0],$verify_rule[2] ?? "");
                break;
            case 'egt':
                return $this->egt($verify_rule[0],$verify_rule[2] ?? "");
                break;
            case 'lt':
                return $this->lt($verify_rule[0],$verify_rule[2] ?? "");
                break;
            case 'elt':
                return $this->elt($verify_rule[0],$verify_rule[2] ?? "");
                break;
            case 'eq':
                return $this->eq($verify_rule[0],$verify_rule[2]);
                break;
            case 'spot':
                return $this->spot($verify_rule[0],$verify_rule[2] ?? 2);
                break;
            case 'sc':
                return $this->specialCharacter($verify_rule[0]);
                break;
            case 'in':
                return $this->in($verify_rule[0],$verify_rule[2]);
                break;
            case 'notin':
                return $this->notin($verify_rule[0],$verify_rule[2]);
                break;
            case 'phone':
                return $this->phone($verify_rule[0]);
                break;
            case 'array':
                return is_array($this->data[$verify_rule[0]]);
                break;
            case 'email':
                return $this->email($verify_rule[0]);
                break;
        }
    }

    /**
     * 验证必须有值
     *
     * @param string|int $verify_field
     * @param array $message
     * @return void
     * @author EricGU178
     */
    private function require($verify_field):bool
    {
        if (is_array($this->data[$verify_field])) {
            return count($this->data[$verify_field]) == 0;
        }
        return trim((string)$this->data[$verify_field]) == '';
    }

    /**
     * 小数点 n 位(包括整数 n-1位)
     *
     * @param string $verify_field
     * @param string $message
     * @param array $data
     * @param integer $ext
     * @return void
     * @author EricGU178
     */
    private function spot($verify_field,$ext=2)
    {
        return !preg_match("/^[0-9]+(.[0-9]{1,$ext})?$/", $this->data[$verify_field]);
    }

    /**
     * 验证是否为整数
     */
    private function verifyInteger($verify_field):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        return !preg_match("/^[0-9][0-9]*$/" ,$this->data[$verify_field]);
    }

    // 大于
    private function gt($verify_field , $ext = 0):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        if ($ext != 0) {
            if (!is_numeric($ext) && $this->data[$verify_field] <= $this->data[$ext]) {
                 return true;
            }
            if (is_numeric($ext) && $this->data[$verify_field] <= $ext) {
                return true;
            } 
        } else {
            if ($this->data[$verify_field] <= $ext) {
                return true;
            }
        }
        return false;
    }

    // 大于等于
    private function egt($verify_field , $ext = 0):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        if ($ext != 0) {
            if (!is_numeric($ext) && $this->data[$verify_field] < $this->data[$ext]) {
                return true;
            }
            if (is_numeric($ext) && $this->data[$verify_field] < $ext) {
                return true;
            } 
        } else {
            if ($this->data[$verify_field] < $ext) {
                return true;
            }
        }
        return false;
    }

    // 小于
    private function lt($verify_field , $ext = 0):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        if ($ext != 0) {
            if (!is_numeric($ext) && $this->data[$verify_field] >= $this->data[$ext]) {
                return true;
            }
            if (is_numeric($ext) && $this->data[$verify_field] >= $ext) {
                return true;
            } 
        } else {
            if ($this->data[$verify_field] >= $ext) {
                return true;
            }
        }
        return false;
    }

    // 小于等于
    private function elt($verify_field , $ext = 0):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        if ($ext != 0) {
            if (!is_numeric($ext) && $this->data[$verify_field] > $this->data[$ext]) {
                return true;
            }
            if (is_numeric($ext) && $this->data[$verify_field] > $ext) {
                return true;
            } 
        } else {
            if ($this->data[$verify_field] > $ext) {
                return true;
            }
        }
        return false;
    }

    // 等于
    private function eq($verify_field,$ext):bool
    {
        if (!is_numeric($this->data[$verify_field])) {
            return false;
        }

        if (!is_numeric($ext) && $this->data[$verify_field] != $this->data[$ext]) {
            return true;
        }
        if (is_numeric($ext) && $this->data[$verify_field] != $ext) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否含有特殊字符
     *
     * @param 待验证的字符串
     * @return string|void
     * @author EricGU178
     */
    private function specialCharacter($verify_field)
    {
        return !preg_match("/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u",$this->data[$verify_field]);
    }

    // 是否含有
    private function in($verify_field,$ext)
    {
        if (!is_array($this->data[$ext])) {
            \trigger_error('in规则扩展字段必须是数组',E_USER_ERROR);
        }
        return !in_array($this->data[$verify_field],$this->data[$ext]);
    }

    // 是否不含有
    private function notin($verify_field,$ext)
    {
        if (!is_array($this->data[$ext])) {
            \trigger_error('notin规则扩展字段必须是数组',E_USER_ERROR);
        }
        return in_array($this->data[$verify_field],$this->data[$ext]);
    }

    /**
     * 电话号正确与否
     *
     * @param string $verify_field
     * @return bool
     * @author EricGU178
     */
    private function phone($verify_field)
    {
        return !preg_match('/^1[345678]{1}[0-9]{9}$/',$this->data[$verify_field]);
    }

    /**
     * 邮箱正确与否
     *
     * @param string $verify_field
     * @return void
     * @author EricGU178
     */
    private function email($verify_field)
    {
        return !preg_match('/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims',$this->data[$verify_field]);
    }
}

