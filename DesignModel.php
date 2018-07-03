<?PHP

echo " 1. 适配器模式<br>";
echo "  适配器设计模式只是将某个对象的接口适配为另一对象所期望的接口<br>";
class errorObject
{
    private $__error;

    public function __construct( $error )
    {
        $this->__error = $error;
    }

    public function getError()
    {
        return $this->__error;
    }
}
class logToConsole
{
    private $__errorObject;

    public function __construct( $errorObject )
    {
        $this->__errorObject = $errorObject;
    }

    public function write()
    {
        echo "logToConsole : write 成功<br>";
    }
}
$error = new errorObject('404 : Not Found');
$log = new logToConsole($error);
$log->write();

class logToCsv {
    const CSV_LOCATION ='log.csv';

    private $__errorObject ;
    public function __construct( $errorObject )
    {
        $this->__errorObject = $errorObject;
    }

    public function write()
    {
        $line = $this->__errorObject->getErrorNumber();
        $line .=',';
        $line .= $this->__errorObject->getErrorText();
        $line .='<br>';
        echo 'logToCsv'.$line;
    }
}

class logToCsvAdapter extends errorObject
{
    private $__errorNumber ,$__errorText;

    public function __construct($error)
    {
        parent::__construct($error);
        $parts = explode(':',$this->getError());
        $this->__errorNumber = $parts[0];
        $this->__errorText = $parts[1];
    }

    public function getErrorNumber()
    {
        return $this->__errorNumber;
    }

    public function getErrorText()
    {
        return $this->__errorText;
    }

}

$error = new logToCsvAdapter("500:Not Found");
$log = new logToCsv($error);
$log->write();

echo " 2. 建造者模式<br>";
echo "  建造者设计模式定义了处理其他对象的复杂构建的对象设计<br>";

class product
{
    protected $_type = '';
    protected $_size = '';
    protected $_color = '';

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @param string $size
     */
    public function setSize($size)
    {
        $this->_size = $size;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->_color = $color;
    }
}
class productBuilder
{
    protected $_product = null;
    protected $_config = [];

    public function __construct( $config )
    {
        $this->_product = new product();
        $this->_config = $config;
    }

    protected function build()
    {
        $this->_product->setType($this->_config['type']);
        $this->_product->setSize($this->_config['size']);
        $this->_product->setColor($this->_config['color']);
    }

    public function getProduct()
    {
        $this->build();
        return $this->_product;
    }
}

$builder = new productBuilder(['type'=>'sss','size'=>43,'color'=>'red']);
$product = $builder->getProduct();
print_r($product);
echo "<br>";
echo " 3. 数据访问对象模式<br>";
echo "  数据访问对象设计模式描述了如何创建提供透明访问任何数据源的对象<bt>";
define("DB_USER",'user');
define("DB_PASS",'pass');
define("DB_HOST",'localhost');
define("DB_DATABASE",'test');
abstract class baseDAO
{
    private $__connection ;
    protected $_tableName = '';
    protected $_primaryKey = '';

    public function __construct()
    {
        $this->__connection = $this->__connectToDB(DB_USER,DB_PASS,DB_HOST,DB_DATABASE);
    }

    private function __connectToDB( $user,$pass,$host,$database)
    {
        echo "{$user},{$pass},{$host},{$database} <br>";
        return "{$user},{$pass},{$host},{$database}";
    }

    public function fetch( $value ,$key = null )
    {
        if( is_null($key)){
            $key = $this->_primaryKey;
        }
        echo "select * from {$this->_tableName} where {$key} = '{$value}'<br>";
    }
}

class userDAO extends baseDAO
{
    protected $_tableName = 'User';
    protected $_primaryKey = 'user_id';

    public function getUserByFirstName()
    {
        $this->fetch('12');
    }
}

$user = new userDAO();
$user->fetch(11);

echo " 4. 装饰器模式<br>";
echo "  如果已有对象的部分内容或功能性发生改变，但是不需要修改原始对象的结构，那么使用装饰器设计模式最合适<br>";
class CD
{
    public $trackList ;

    public function __construct()
    {
        $this->trackList = [];
    }

    public function addTrack( $track )
    {
        if( is_array($track) ){
            $this->trackList = array_merge($track,$this->trackList);
        }else{
            $this->trackList[] = $track;
        }
    }

    public function getTrackList()
    {
        $output = '';
        foreach ( $this->trackList as $num => $value ){
            $num += 1;
            $output .= "{$num}  {$value} <br>";
        }
        echo $output.'<br>';
        return $output;
    }
}
$arr = ['What It Neans','Brr','Goodbye'];
$cd = new CD();
$cd->addTrack($arr);
$cd->getTrackList();

class CDTrackListDecoratorCaps
{
    protected $__cd;
    public function __construct( CD $cd )
    {
        $this->__cd = $cd;
    }

    public function makeCaps()
    {
        foreach ($this->__cd->trackList as $num => $track ){
            $this->__cd->trackList[$num] = strtoupper($track);
        }
    }
}
$myCDCaps = new CDTrackListDecoratorCaps($cd);
$myCDCaps->makeCaps();
$cd->getTrackList();

echo " 5. 委托模式<br>";
echo "  通过分配或委托至其他对象，委托设计模式能够去除核心对象中的判断和复杂的功能性<br>";
class newPlayList
{
    private $__songs;
    private $__typeObject;

    public function __construct( $type )
    {
        $this->__songs = [];
        $object = "{$type}PlayList";
        $this->__typeObject = new $object;
    }

    public function addSong( $location,$title )
    {
        $song = ['location'=>$location,'title'=>$title];
        $this->__songs[] = $song;
    }

    public function getPlayList()
    {
        $playList = $this->__typeObject->getPlayList($this->__songs);
        echo $playList.'<br>';
        return $playList;
    }
}

class mp3PlayList
{
    public function getPlayList( $songs )
    {
        $mp3Str = " mp3PlayList<br>";
        foreach ($songs as $key =>$song ){
            $mp3Str .= " {$song['location']} : {$song['title']}<br>";
        }
        return $mp3Str;
    }
}

class plsPlayList
{
    public function getPlayList( $songs )
    {
        $mp3Str = " plsPlayList<br>";
        foreach ($songs as $key =>$song ){
            $mp3Str .= " {$song['location']} : {$song['title']}<br>";
        }
        return $mp3Str;
    }
}

$playObject = new newPlayList('mp3');
$playObject->addSong('ss.mp3','天亮了');
$playObject->addSong('aa.mp3','兄弟');
$playObject->getPlayList();


$playObject = new newPlayList('pls');
$playObject->addSong('bb.pls','冰雨');
$playObject->addSong('cc.pls','爱你');
$playObject->getPlayList();

echo " 6. 外观模式<br>";
echo "  通过在必需的逻辑和方法的集合前创建简单的外观接口，外观设计模式隐藏了来自调用对象的复杂性<br>";
class CDObject
{
    public $tracks = [];
    public $band = '';
    public $title = '';

    public function __construct( $title ,$band , $tracks )
    {
        $this->title = $title;
        $this->band = $band;
        $this->tracks = $tracks;
    }
}

class CDUpperCase
{
    public static function makeString( CDObject $CDObject , $type )
    {
        $CDObject->$type = strtoupper($CDObject->$type);
    }

    public static function makeArray( CDObject $CDObject , $type )
    {
        $CDObject->$type = array_map('strtoupper',$CDObject->$type );
    }
}

class CDMakeXML
{
    public static function create( CDObject $CDObject)
    {
        echo "CDMakeXML <br> ";
        echo " title : {$CDObject->title} <br> ";
        echo " band : {$CDObject->band} <br> ";
        echo " CDObject List  :  <br> ";
        foreach ( $CDObject->tracks as $key => $value ){
            echo "{$value} <br>";
        }
    }
}

class webServiceFacade
{
    public static function makeXMLCall( CDObject $CDObject )
    {
        CDUpperCase::makeString($CDObject,'title');
        CDUpperCase::makeString($CDObject,'band');
        CDUpperCase::makeArray($CDObject,'tracks');
        CDMakeXML::create($CDObject);
    }
}

$cdobject = new CDObject('我喜欢的歌曲','三星',['What It Neans','Brrr','GoodBye']);
webServiceFacade::makeXMLCall($cdobject);

echo "<br><br>";

echo " 7. 工厂模式<br>";
echo "  工厂设计模式提供获取某个对象的新实例的一个接口，同时使用调用代码避免确定实际实例化基类的步骤<br>";

class classCD
{
    public $title = '';
    public $band = '';
    public $tracks = [];

    public function setTitle( $title )
    {
        $this->title = $title;
    }

    public function setBand( $band )
    {
        $this->band = $band;
    }

    public function addTrack( $track )
    {
        if( is_array($track) ){
            $this->tracks = array_merge( $track,$this->tracks);
        }else{
            $this->tracks[] = $track;
        }
    }
}

class enhancedCD
{
    public $title = '';
    public $band = '';
    public $tracks = [];

    public function __construct()
    {
        $this->tracks[] = 'DATA TRACK';
    }

    public function setTitle( $title )
    {
        $this->title = $title;
    }

    public function setBand( $band )
    {
        $this->band = $band;
    }

    public function addTrack( $track )
    {
        if( is_array($track) ){
            $this->tracks = array_merge( $track,$this->tracks);
        }else{
            $this->tracks[] = $track;
        }
    }
}

class CDFactory
{
    /**
     * @param $type
     * @return mixed
     */
    public static function create( $type )
    {
        $class = strtolower($type).'CD';
        return new $class;
    }
}

$title = 'Waste of a Rib ';
$band = ' Never Again ';
$tracks = ['What It Means ','Brrr' , 'GoodBye'];
$cd = CDFactory::create('enhanced');
$cd->setTitle($title);
$cd->setBand($band);
$cd->addTrack($tracks);
print_r($cd);
echo '<br>';

$cd = CDFactory::create('class');
$cd->setTitle($title);
$cd->setBand($band);
$cd->addTrack($tracks);
print_r($cd);
echo '<br>';
echo '<br>';


echo " 8. 解释器模式<br>";
echo " 解释器设计模式用于分析一个实体的关键元素，并且针对每个元素的提供自己的解释或相应的动作 <br>";

class user
{
    protected $_userName = '';

    public function __construct( $userName )
    {
        $this->_userName = $userName;
    }

    public function getProFilePage()
    {
        $profile = "<h2> I like Never Again ! </h2>";
        $profile .= "<h2> user : {$this->_userName} </h2>";
        echo $profile ;

        $myUser = new userCD();
        $myUser->setUser($this->_userName);
        $myUser->getTitle();

        return $profile;
    }
}

class userCD
{
    protected $_user = null;
    public function setUser( $user )
    {
        $this->_user = $user;
    }

    public function getTitle()
    {
        $title = " Waste of a Rib userCD <br>";

        echo $title;
        return $title;
    }
}

class userCDInterpreter
{
    protected $_user = null ;

    public function setUser( $user )
    {
        $this->_user = $user;
    }

    public function getInterpreted()
    {
        return $this->_user->getProfilePage();
    }
}

$userName = 'aaron';
$user = new user($userName);
$interpreter = new userCDInterpreter();
$interpreter->setUser($user);
$interpreter->getInterpreted();


echo " 9. 迭代器模式<br>";
echo "   迭代器设计模式可以帮助构造特定对象，那些对象能够提供单一标准接口循环或迭代任何类型的可计数数据<br>";
echo '<br>';

class splCD
{
    public $band = '';
    public $title = '';
    public $trackList = [];

    public function __construct( $title,$band )
    {
        $this->title = $title;
        $this->band = $band;
    }

    public function addTrack( $track )
    {
        $this->trackList[] = $track;
    }
}

class CDSearchByBandIterator implements Iterator
{
    private $__CDs = [];
    private $__valid = FALSE;

    public function __construct( $bandName )
    {
        $arr[] = ['title'=>'title - 1 ','band'=>'band - 1'];
        $arr[] = ['title'=>'title - 2 ','band'=>'band - 2'];
        $arr[] = ['title'=>'title - 3 ','band'=>'band - 3'];
        $arr[] = ['title'=>'title - 4 ','band'=>'band - 4'];
        $arr[] = ['title'=>'title - 5 ','band'=>'band - 5'];
        foreach ( $arr as $value ){
            $cd = new splCD($value['title'],$value['band']);
            $cd->addTrack($value['title']."--".$value['band']);
            $this->__CDs[] = $cd;
        }
        echo $bandName."<br>";
    }

    public function getCDs()
    {
        return $this->__CDs;
    }

    public function next()
    {
        $this->__valid = next( $this->__CDs) === FALSE ? FALSE:TRUE;
    }

    public function rewind()
    {
        $this->__valid  = (reset($this->__CDs) === FALSE) ? FALSE:TRUE;
    }

    public function valid()
    {
        return $this->__valid;
    }

    public function current()
    {
        return current($this->__CDs);
    }

    public function key()
    {
        return key($this->__CDs);
    }
}

$queryItem = "Never Again ";
$cds = new CDSearchByBandIterator($queryItem);
//$cds = $cds->getCDs();
foreach ($cds as $value ){
    echo $value->title .'--'.$value->band.'<br>';
}
echo "<br>";
echo "<br>";
echo "<br>";
echo " 10. 中介者模式<br>";
echo "   中介者设计模式用于开发一个对象，这个对象能够在类似对象相互之间不直接交互的情况下传送或调解对这些对象的集合的修改。<br>";

class intermediaryCD
{
    public $band = '';
    public $title = '';

    protected $_mediator;

    public function __construct( $mediator = null)
    {
        $this->_mediator = $mediator;
    }

    public function save()
    {
        echo " intermediaryCD ->save() <br>";
    }

    public function changeBandName( $newName )
    {
        if( !is_null( $this->_mediator ) ){
            $this->_mediator->change( $this,['band'=>$newName]);
        }
    }
}

class MP3Archive
{
    public $band = '';
    public $title = '';

    protected $_mediator;

    public function __construct( $mediator = null)
    {
        $this->_mediator = $mediator;
    }

    public function save()
    {
        echo " MP3Archive ->save() <br>";
    }

    public function changeBandName( $newName )
    {
        if( !is_null( $this->_mediator ) ){
            $this->_mediator->change( $this,['band'=>$newName]);
        }
    }

}

class MusicContainerMediator
{
    protected $_containers = [];

    public function __construct()
    {
        $this->_containers[] = 'intermediaryCD';
        $this->_containers[] = 'MP3Archive';
    }

    public function change( $object , $newValue )
    {
        $title = $object->title;
        $band = $object->band;
        foreach ( $this->_containers as $container ){
            echo $container.'<br>';
            if( !( $object instanceof $container )){
                $object2 = new $container;
                $object2->title = $title;
                $object2->band = $band;
                foreach ( $newValue as $key=>$value ){
                    $object2->$key = $value;
                }
                $object2->save();
            }
        }
    }
}

$band = "Never band ";
$title = "Never title ";
$mediator = new MusicContainerMediator();
$cd = new intermediaryCD($mediator);
$cd->title = $title;
$cd->band = $band;
$cd->changeBandName('Maybe Once More ');

echo "<br>";
echo "<br>";

echo " 11. 观察者模式<br>";
echo "   观察者设计模式能够更便利的创建查看目标对象状态的对象，并且提供与核心对象非耦合的指定功能性 。<br>";
class observeCD
{
    public $title = '';
    public $band = '';

    protected $_observers = [];

    public function __construct( $title , $band )
    {
        $this->band = $band;
        $this->title = $title;
    }

    public function attachObserver( $type , $observer )
    {
        $this->_observers[$type][] = $observer;
    }

    public function notifyObserver($type)
    {
        if( isset($this->_observers[$type])){
            foreach ( $this->_observers[$type] as $observer ){
                $observer->update($this);
            }
        }
    }

    public function buy()
    {
        $this->notifyObserver('purchased');
    }
}

class buyCDNotifyStreamObserver
{
    public function update( observeCD $CD)
    {
        $activity = " The CD maned {$CD->title} by ";
        $activity .= "{$CD->band} was Just Purchased ";
        echo ' buyCDNotifyStreamObserver '.$activity."<br>";
        activityStream::addNewItem($activity);

    }
}

class activityStream
{
    public static function addNewItem( $item )
    {
        print_r($item);
        echo '<br>';
    }
}

$title = "Waste of a Rib ";
$band = " Never Again ";
$cd = new observeCD($title,$band);

$observer = new buyCDNotifyStreamObserver();
$cd->attachObserver('purchased',$observer);
$cd->buy();

echo "<br>";
echo "<br>";
echo " 13. 原型模式<br>";
echo "  原型设计模式创建对象的方式是复制和克隆初始对象或原型，这种方式比创建新实例更为有效<br>";

class prototypeCD
{
    public $band = '';
    public $title = '';
    public $trackList = [];
    public function __construct()
    {
        $this->title = 'prototype_CD';
        $this->band = '三星';
    }

    public function buy()
    {
        print_r($this);
        echo "<br>";
    }
}

class MixtapeCD extends prototypeCD
{
    public function __clone()
    {
        $this->title = 'Mixtape';
    }
}

$mixtapeCD = new MixtapeCD();
$purchase[] = ['brrr','goodbye'];
$purchase[] = ['what it means ','brrr'];
foreach ( $purchase as $value ){
    $cd = clone $mixtapeCD;
    $cd->trackList = $value;
    $cd->buy();
}
echo "<br>";
echo "<br>";
echo " 14. 代理模式<br>";
echo "  代理设计模式构建了透明置于两个不同对象之内的一个对象，从而能够裁取或代理这两个对象间的通信或访问 <br>";

class ProxyCD
{
    protected $_title = '';

    protected $_band = '';

    protected $_handle = null;

    public function __construct( $title ,$band )
    {
        $this->_title = $title;
        $this->_band = $band;
    }

    public function _connect()
    {
        echo "ProxyCD connect <br>";
    }

    public function  buy()
    {
        $this->_connect();
        echo " ProxyCD buy <br>";
    }
}

class DallasNOCCDProxy extends ProxyCD
{
    public function _connect()
    {
        echo "DallasNOCCDProxy connect <br>";
    }
}

$title = 'Waste of a Rib ';
$band = 'Never Again ';
$cd = new DallasNOCCDProxy($title,$band);
$cd->buy();
echo "<br>";
echo "<br>";
echo "<br>";


echo " 15. 单元素模式（单例）<br>";
echo "  通过提供对自身共享实例的访问，单元素设计模式用于限制特定对象只能被创建一次。<br>";

class InventoryConnection
{
    protected static $_instance = null ;

    protected $_handle = null;

    public static function getInstance()
    {
        if( ! self::$_instance instanceof self ){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function __construct()
    {
        $this->_handle = 'localhost';
        echo $this->_handle.'<br>';
    }

    public function updateQuery( $title , $band )
    {
        echo " InventoryConnection -> updateQuery <br>";
        echo " title ： {$title} ， band : {$band} <br>";
    }

}
class InventoryCD
{
    protected $_title = '';

    protected $_band = '';

    public function __construct( $title , $band )
    {
        $this->_title = $title;
        $this->_band = $band;
    }

    public function buy()
    {
        $inventory = InventoryConnection::getInstance();
        $inventory->updateQuery($this->_title,$this->_band);
    }
}

$InventoryCD[] = ['band'=>'New Again ', 'title'=>' Waster of a Rib '];
$InventoryCD[] = ['band'=>'Therapee','title'=>'Long Road '];
foreach ($InventoryCD as $value)
{
    $cd = new InventoryCD($value['title'],$value['band']);
    $cd->buy();
}
echo "<br>";
echo "<br>";
echo " 16. 策略模式<br>";
echo "  策略设计模式帮助构建的对象不必自身包含逻辑，而是能够根据需要利用其他对象中的算法。<br>";
$title = 'Never Again';
$band = 'Waste of a Rib ';
class CDUserStrategy
{
    public $title = '';

    public $band = '';

    protected $_strategy ;

    public function __construct( $title, $band )
    {
        $this->title = $title;
        $this->band  = $band;
    }

    public function setStrategyContext( $strategyObject )
    {
        $this->_strategy = $strategyObject;
    }

    public function get()
    {
        return $this->_strategy->get($this);
    }
}

class CDAsXMLStrategy
{
    public function get( CDUserStrategy $cd )
    {
        echo "CDAsXMLStrategy {$cd->title} , {$cd->band} <br>";
    }
}

class CDAsJSONStrategy
{
    public function get( CDUserStrategy $cd )
    {
        echo "CDAsJSONStrategy {$cd->title} , {$cd->band} <br>";
    }
}

$cd = new CDUserStrategy( $title , $band );
$cd->setStrategyContext( new CDAsXMLStrategy());
$cd->get();

$cd->setStrategyContext( new CDAsJSONStrategy());
$cd->get();

echo "<br>";
echo " 17. 模板模式<br>";
echo "  模板设计模式创建了一个实施一组方法和功能的抽象对象，子类通常将这个对象作为模板用于自己的设计 。 <br>";

abstract class SaleItemTemplate
{
    public $price = 0;

    public final function setPriceAdjustments()
    {
        $this->price += $this->taxAddition();
        $this->price += $this->overSizeAddition();
        echo get_class($this).' --- '.$this->price."<br>";
    }

    protected function overSizeAddition()
    {
        return 0 ;
    }

    abstract protected function taxAddition();
}

class SaleItemCD extends SaleItemTemplate
{
    public $brand;

    public $title;

    public function __construct( $title , $band ,$price )
    {
        $this->title = $title;
        $this->brand = $band;
        $this->price = $price;
    }

    public function taxAddition()
    {
        return round($this->price * 0.5,2);
    }
}

class SaleItemBand extends SaleItemTemplate
{
    public $band;

    public function __construct( $band , $price )
    {
        $this->price = $price;
        $this->band  = $band;
    }

    protected function taxAddition()
    {
        return 0 ;
    }

    protected function overSizeAddition()
    {
        return round( $this->price * 0.2, 2 );
    }
}

$title = 'CD Waste of a Rib ';
$band = 'Never Again ';
$price = 100;
$band_price = 100;

$cd = new SaleItemCD( $title , $band , $price );
$cd->setPriceAdjustments();

$band_cd = new SaleItemBand( $band , $band_price );
$band_cd->setPriceAdjustments();

echo '<br>';
echo '<br>';

echo " 18. 访问者模式<br>";
echo "  访问者设计模式构造了包含某个算法的截然不同的对象，在父对象以标准方式使用这些对象时就会将该算法应用于父对象。<br>";

class VisitCD
{
    public $band;
    public $title;
    public $price;

    public function __construct( $band , $title , $price )
    {
        $this->title = $title;
        $this->band  = $band ;
        $this->price = $price;
    }

    public function buy ()
    {
        echo " VisitCD -- buy <br>";
    }

    public function acceptVisitor( $visitor )
    {
        $visitor->visitCD($this);
    }
}

class CDVisitorLogPurchase
{
    public function visitCD( VisitCD $cd )
    {
        $logline = "{$cd->title} by {$cd->band} was purchased for {$cd->price} <br>";
        echo 'CDVisitorLogPurchase -- '.$logline;
    }
}

class CDVisitorDiscount
{
    public function visitCD( VisitCD $cd )
    {
        if( $cd->price > 100 ){
            $this->_discountList( $cd );
        }
        $logline = "{$cd->title} by {$cd->band} was purchased for {$cd->price} <br>";
        echo 'CDVisitorDiscount -- '.$logline;
    }

    protected function _discountList( VisitCD $cd )
    {
        $cd->price += $cd->price * 0.5;
    }
}

$title = 'Waste of  a Rib ';
$band = 'Never Again';
$price = 100 ;

$cd = new VisitCD( $band , $title , $price );
$cd->buy();
$cd->acceptVisitor( new CDVisitorLogPurchase() );
$cd->acceptVisitor( new CDVisitorDiscount() );
