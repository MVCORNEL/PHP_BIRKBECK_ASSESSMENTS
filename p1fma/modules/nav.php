<?php	
#function used to display  the navigation based on a page number given and from
#@param = @pageNumber expects a integer value form 1 to 3 1-index ,2-intranet , 3-admin
function showNav($pageNumber){
	#no navigation displayed
	if(!in_array($pageNumber, array(1,2,3))){
		return;
	}
	$isLogin=false;
	$isAdmin=false;
	#check if a user is logged or not based on session global array
	if(isset($_SESSION['admin'])){
		$isLogin=true;
		if($_SESSION['admin']==1){
			$isAdmin=true;
		}
	}		
	$href="href= ";	
	#all the href that will be used to naviate between the navigation bar, each page exludes itslef
	#page 1 won't have any href to itself, instead will have # , this will apply for the other links aswell
	$hrefHome=$pageNumber===1 ? 	$href."#": $href."'index.php'";
	$hrefIntranet=$pageNumber===2 ? 	$href."#": $href."'intranet.php'";
	$hrefAdmin=$pageNumber===3 ? 	$href."#": $href."'admin.php'";
	#list element used for the navigation as string variables
	$liHome="<li><a $hrefHome>Home</a> </li>";
	$liIntranet= $isLogin ? "<li><a $hrefIntranet>Intraner</a></li>" : "";	
	$liAdmin= ($isLogin && $isAdmin) ? "<li><a $hrefAdmin>Administrator</a></li>" : "";
	#display navigation
	echo 
	"<nav id='primarynav'>
		<ul>
		$liHome
		$liIntranet
		$liAdmin
		</ul>
	</nav>	";	
	}
?>