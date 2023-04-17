<?php

$colours = Array(
	"4"=>"4process",
	"1"=>"black",
	"0"=>"none"
);

class cRegistration {
    public $name;
    public $email;
    public $username;
    public $password;
    public $defaultRole;
}

class cWall {
    public $capacity;
    public $name;
}

class cEventType {
    public $code;
    public $name;
    public $description;
    public $phase;
}

class cEvent {
    public $step;
    public $code;
    public $eventTypeId;
    public $name;
    public $phase;
    public $description;
    public $wallId;
    public $wallName;
    
    function __construct($step, $event){
		$this->name = $event->name;
		$this->code = $event->code;
		$this->description = $event->description;
		$this->phase = $event->phase;
		$this->eventTypeId = $event->_id;
		$this->step = $step;
		
		if ($this->phase!="reconcile")	{
			unset($this->wallId);
			unset($this->wallName);
		}

		unset($this->_id);
		unset($this->__v);
		unset($this->averageTime);
		unset($this->setupTime);
		
    }
}

class cFinish     {
    public $side1;
    public $side2;
    function __construct(){
	    $this->side1 = "none";
	    $this->side2 = "none";
    }
}

class cColour     {
    public $side1;
    public $side2;
    function __construct(){
	    $this->side1 = "none";
	    $this->side2 = "none";
    }
}

class cComponent	{
    public $type;
    public $code;
    public $stockId;
    public $width;
    public $height;
    public $paper;
    public $weight;
    public $pages;
    public $ticketTemplate;
    
    function __construct(){
	    $this->colour = new cColour();
	    $this->finish = new cFinish();
	    $this->route = Array();
    }
}

class cProduct	{

    public $accountId;
    public $imposedInternally;
    public $description;
    public $shrinkWrap;
    public $binding;
    public $folding;
    public $bulkQuantity;
    public $unitWeight;
    public $productCode;
    
    function __construct(){
	    $this->imposedInternally = false;
	    $this->shrinkWrap = false;
	    $this->bulkQuantity = false;
	    $this->unitWeight = 10;
	    $this->folding = "none";
	    $this->binding = "none";
	    $this->components = Array();
    }
    
}

class cClient	{
    public $active;
    public $name;
    public $wallId;
    public $couriers;
}

class cCourier {
	public $courierId;
	public $courierCode;
	public $courierService;
	public $username;
	public $password;
	public $isDefault;
}

class cSku	{

    public $accountId;
    public $SLADuration;
    public $active;
    public $code;
    public $courierId;
    public $description;
    public $productId;
    public $productionCutOffTime;
    public $productionStartMethod;
    
}

class cPaper	{
	public $name;
	public $code;
    function __construct($name, $code){
	    $this->name = $name;
	    $this->code = $code;
	}
}

class cObject	{

}


?>