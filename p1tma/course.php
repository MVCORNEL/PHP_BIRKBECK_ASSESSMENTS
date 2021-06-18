<?php
#Include mmmr function
include 'includes/function.php';

/************************************************************** FILES DATA ****************************************************************/
/*
function returns a list of readable txt files paths based on a directory name 
@param[$local_dir] = string
@return array or FALSE 
 */
function getTxtFilesPath( $local_dir ) {
	#array to hold all valid txt files paths
    $textFiles = array(); 
    if ( $handle = opendir( $local_dir ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
			#create file path of the file directory, and the files within the directory
            $filePath = $local_dir . '/' . $file;
			#skip '.' '..' files but also unreadable files
			if ( $file == '.' || $file == '..' || !is_readable( $filePath ) ) {
                continue;
            } 
			#display and skip files that do not have .txt extension
            if ( pathinfo( $file, PATHINFO_EXTENSION ) != "txt" ) { 
                echo "<p>$file : INVALID FILE EXTENSION- should be .txt </p>";
                continue;
            }
			#if files passed the validations, store them into array
            $textFiles[] = $filePath; 
        }
        closedir( $handle );
    } 
	else {
        echo '<p> Error openning directory' . $local_dir . '</p>';
        return FALSE;
    }
    return $textFiles;
}

/*
function used to store data from a text file into an array and return it
@param[$filePath] = string 
@return array or FALSE 
*/
function getRawFileData( $filePath ) {
	#open file stream
    if ( $handle = fopen( $filePath, 'r' ) ) {
        while ( !feof( $handle ) ) {
			//remove white space before and after each line and store each line data into an array
            $courseData[] = trim( fgets( $handle ) );
        }
        return $courseData;
    }
	else{
		return FALSE;
		echo '<p> Error openning the file' . $local_dir . '</p>';
	}
}

/*
function used to get file name and its extension based on a file path ex: 'directory/file.txt'  as 'file.txt'
@param[$filePath] = string 
@return string
*/
function getFileName( $filePath ) {
    $courseFileName = pathinfo( $filePath, PATHINFO_BASENAME );
    return $courseFileName;
}

/************************************************************** HEADER ****************************************************************/
/*
function validates and returns an array that represents header's specification fields(MODULE CODE,MODULE NAME,TUTOR NAME,MARKING DATE)
-each specification field is represented as an array of 3 elements('value','isValid','errorType')
-each independed specification is validated separately through its own validation function
@param[$fileData] = array
@return nested array or FALSE 
 */
function getValidatedHeader( $fileData ) {
	//check if it parameter passed is not an array
	if ( !is_array( $fileData ) ) {
        echo '<p>Invalid parammeter to getValidatedHeader() function: ' . $fileData . ' is not an array</p>';
        return FALSE;
    }
	//check if the array is empty
	if ( empty( $fileData ) ) {
		echo '<p>Empty array as parameter to getValidatedHeader() function </p>';
        return FALSE;
    }
	#check if the first line representing header's data is empty
	if ( empty( $fileData[0] ) ) {
		echo '<p>Empty header line to getValidatedHeader() function </p>';
        return FALSE;
    }
	
	#store header data into a variable
	$headerData = $fileData[0];
	#array used to store each header specification(MODULE CODE,MODULE NAME,TUTOR NAME,MARKING DATE)
    $header_array = array();
	#split header data into 4 individual words, and trim the white spaces
    $headerWords = array_map( 'trim', explode( ',', $headerData ) );
	#iterate and validate each individual word
    foreach ( $headerWords as $index => $word ) {
        //iterate through each header words
        switch( $index ) {
            case 0 ://MODULE CODE
            $header_array['Module code'] = validatedModuleCode( $word );
            break;
            case 1 ://MODULE NAME
			#module name code use to verify is the module name matches with the module name code
			$moduleCode = substr( $headerData, 0, 2 );
            $header_array['Module Title'] = validatedModuleName( $word,$moduleCode);
            break;
            case 2 ://TUTOR NAME
            $header_array['Tutor'] = validatedTutorName( $word );
            break;
            case 3 ://MARKING DATE
            $header_array['Marked date'] = validatedDate( $word );
            break;
        }
    }
    return $header_array;
}

/*
function wraps a module code into an array that contains its validation details(value,isValid,errorType)
function consists in three validation steps: module code validation, year validation and term validation
@param[$code] = string 
@return array
*/
function validatedModuleCode( $code ) {
	#initialize the array and the array elements that will contains module code validation details
    $code_array = array();
    $code_array['value'] = $code;
    $code_array['isValid'] = TRUE;
    $code_array['errorType'] = '';
	#pointless to check for further errors, because each individual word will be wrongly defined if the code length is different of 8
    if ( strlen( $code ) != 8 ) {
        $code_array['errorType'] = ": Invalid number of characters(8 required)";
        $code_array['isValid'] = FALSE;
        return $code_array;
    }
	
    $charModule = substr( $code, 0, 2 );
    $charYear = substr( $code, 2, 4 );
    $charTerm = substr( $code, 6, 2 );
	#allowed module codes
    $moduleList = array( 'PP', 'P1', 'DT' );
	#allowed term codes
    $termList = array( 'T1', 'T2', 'T3' );
	
	#check if the module code is valid
    if ( !in_array( $charModule, $moduleList ) ) {
        $code_array['errorType'] = ": Invalid module code";
        $code_array['isValid'] = FALSE;
    }
	
	#check if the year is a whole number
    if ( !isWholeNumber( $charYear ) ) {
        $code_array['errorType'] .= ": Invalid year code";
        $code_array['isValid'] = FALSE;
    }
	
	#check if the term code is valid
    if ( !in_array( $charTerm, $termList ) ) {
        $code_array['errorType'] .= ": Invalid term code";
        $code_array['isValid'] = FALSE;
    }
    return $code_array;
}

/*
function wraps a module name into an array that contains its validation details(value,isValid,errorType)
function consists in three validation steps: check if the module name is empty, check if the module name is valid and after checks
EXTRA if the module name code matches with the module name
@param[$moduleName] = string 
@return array
*/
function validatedModuleName( $moduleName, $moduleNameCode ) {
	#initialize the array and the array elements that will contains module name validation details
    $module_array = array();
    $module_array['value'] = $moduleName;
    $module_array['isValid'] = TRUE;
    $module_array['errorType'] = '';
	
	#allowed module names with module name codes idexes
    $moduleList = array( 'Problem Solving for Programming', 'Web Programming using PHP', 'Introduction to Database Technology' );
	$moduleList['PP']='Problem Solving for Programming';
	$moduleList['P1']='Web Programming using PHP';
	$moduleList['DT']='Introduction to Database Technology';
	#check if the module name is empty
    if ( empty( $moduleName ) ) {
        $module_array['errorType'] .= ": Empty module name";
        $module_array['isValid'] = FALSE;
        return $module_array;
    }
	
	#check if the module name is valid
    if ( !in_array( $moduleName, $moduleList ) ) {
        $module_array['errorType'] .= ": Incorrect module name";
        $module_array['isValid'] = FALSE;
		return $module_array;
    }
	#EXTRA
	#check if the module name matches with the module code
	#only if the moduleNameCode is valid, check if the module code matches with module name
	if(array_key_exists($moduleNameCode,$moduleList) AND !($moduleList[$moduleNameCode]===$moduleName)){
			$module_array['errorType'] .= ": Module code doesn't match module name";
			$module_array['isValid'] = FALSE;
		}
	
	
    return $module_array;
}

/*
function wraps a tutor name into an array that contains its validation details(value,isValid,errorType)
function consists in one validation step: check if the tutor name is empty 
@param[$tutorName] = string 
@return array
*/
function validatedTutorName( $tutorName ) {
	#initialize the array and the array elements that will contains tutor name validation details
    $tutor_array = array();
    $tutor_array['value'] = $tutorName;
    $tutor_array['isValid'] = TRUE;
    $tutor_array['errorType'] = '';
	
	#check if the name is empty
    if ( empty( $tutorName ) ) {
        $tutor_array['errorType'] .= ": Empty tutor name";
        $tutor_array['isValid'] = FALSE;
        return $tutor_array;
    }
    return $tutor_array;
}

/*
function wraps a date into an array that contains its validation details(value,isValid,errorType)
function consists in two validation steps: check if the date is empty, check if the fate format is correct and check if the date is valid.
@param[$date] = string 
@return array
*/
function validatedDate( $date ) {
	#initialize the array and the array elements that will contains date validation details
    $date_array = array();
    $date_array['value'] = $date;
    $date_array['isValid'] = TRUE;
    $date_array['errorType'] = '';
	
	#check if the date is empty
    if ( empty( $date ) ) {
        $date_array['errorType'] .= ": Empty date value";
        $date_array['isValid'] = FALSE;
        return $date_array;
    }
	#check if the date is correcly delimited by 2 /
    if ( substr_count( $date, '/' ) != 2 ) {
        $date_array['errorType'] = ": Invalid date format";
        $date_array['isValid'] = FALSE;
        return $date_array;
    }
	#check if the date is valid
    $date_words = array_map( 'trim',explode( '/', $date ));
	#very important to swap day with-month order, because check date checks month first
    if ( !checkdate( $date_words[1], $date_words[0], $date_words[2] ) ) {
        $date_array['errorType'] = ": Invalid date";
        $date_array['isValid'] = FALSE;
    }
    return $date_array;
}

/************************************************************** MARKS ****************************************************************/

/*
function validates and returns an array that represents students' data
-each student data field is represented as an array of 4 elements('id','grade','isValid',errorType)
-each student data is validated through validation 'validatedMark()' 
@param[$fileData] = array
@return nested array or FALSE 
 */
function getValidatedMarks( $fileData ) {
	#check if it parameter passed is not an array
	if ( !is_array( $fileData ) ) {
        echo '<p>Invalid parammeter to getValidatedHeader() function: ' . $fileData . ' is not an array</p>';
        return FALSE;
    }
	#check if the array is size is less than 2, first mark will start on the second line
    if ( count( $fileData )<2 ){
		echo '<p>No students\' id/marks contained </p>';
		return FALSE;
	}
	#array used to store each individual, validated students' data
    $marks_array = array();
	#iterate through each student'd data. Validate students' data
    for ( $i = 1; $i<count( $fileData ); $i++ ) {
        $marks_array[] = validatedMark( $fileData[$i] );
    }
    return $marks_array;
}

/*
function wraps a student data into an array that contains its validation details(id,grade,isValid,errorType)
function consists in multiple validation steps used to validate student' id and stundent' mark
@param[$markLine] = string 
@return array
*/
function validatedMark( $markLine ) {
   	#initialize the array and the array elements that will contains student' data validation details
    $mark_array = array();
    $mark_array['id'] = '';
    $mark_array['grade'] = '';
    $mark_array['isValid'] = TRUE;
    $mark_array['errorType'] = '';
	
	#split student' data data  2 individual words(id,grade)
    $mark_words = array_map( 'trim', explode( ',', $markLine ) );
	#initialize id
    $studentId = $mark_words[0];
	#initialize grade
    $moduleGrade = $mark_words[1];
	
    #add the (id,grade) to the validated marks_array
    $mark_array['id'] = $studentId;
    $mark_array['grade'] = $moduleGrade;
	
    #initialize words lengths
    $studentIdLength = strlen( $studentId );
    $moduleGradeLength = strlen( $moduleGrade );
	
	#ID VALIDATION
	#check if the id is empty
	if (empty( $studentId )) {
        $mark_array['errorType'] .= ' - Missing student ID ';
        $mark_array['isValid'] = FALSE;
    }
	#if the id is not empty
	else{
		#check if the id is not a whole number or if the id doesen't have 8 elements
		if ( !isWholeNumber( $studentId ) OR $studentIdLength !== 8  ) {
			$mark_array['errorType'] .= ' - Incorrect student ID ';
			$mark_array['isValid'] = FALSE;
		}
	}
	
	#GRADE VALIDATION
	//check if the mark is empty
	if (empty( $studentId )) {
        $mark_array['errorType'] .= ' - Missing mark ';
        $mark_array['isValid'] = FALSE;
    }
	#if the mark is not empty
	else{
		#check if the grade is not a whole number,or if the grade has a wrong number of elements or if the grade range is not between 0-100
		if ( !isWholeNumber( $moduleGrade ) OR ( $moduleGradeLength>3 OR $moduleGradeLength==0 ) OR $moduleGrade>100 ) {  
			$mark_array['errorType'] .= ' - Incorrect mark ';
			$mark_array['isValid'] = FALSE;
		}
	}
	
	#add no included to log error, if something is incorrect
	if(  $mark_array['isValid'] === FALSE){
		$mark_array['errorType'] .= ': not included ';	
	}
	
    return $mark_array;
}

/*
function returns a list of valid grades based on validated student data array. If the pair id/grade 'isValid' the grade will be added to the array
@param[$validatedMarks] = nested array 
@return array
*/
function getValidGrades( $validatedMarks ) {
    #check if the parameter is an array
	if ( !is_array( $validatedMarks ) ) {
        echo '<p>Invalid parammeter to mmmr() function: ' . $validatedMarks . ' is not an array</p>';
        return FALSE;
    }
	#list of valid grades
    $validGrades = array();
	#add valid grades to the validGrades array
    foreach ( $validatedMarks as $mark ) {
        if ( $mark['isValid'] === TRUE ) {
            $validGrades[] = $mark['grade'];
        }

    }
    return $validGrades;
}

/*
function returns an array that represents the module analysis(Mean,Mode,Range)
@param[$grades] = array 
@return array
*/
function getModuleAnalysis( $grades ) {
	//check if input parameter is an array
    if ( !is_array( $grades ) ) {
        echo '<p>Invalid parammeter to getModuleAnalysis() function: ' . $grades . ' is not an array</p>';
        return FALSE;
    }
	
	#initialize array designated to store the module analysis
    $analysis_array = array();
	#mmmr method is imported from function file
    $analysis_array['Mean'] = round(mmmr( $grades, 'mean' ),1);
    $analysis_array['Mode'] = mmmr( $grades, 'mode' );
    $analysis_array['Range'] = mmmr( $grades, 'range' );

    return $analysis_array;
}

/*
function used to calculate and return the overall grade distribution of an array of grades
each mark will be classificated into an array elements(Dist,Merit,Pass,Fail) accordingly with its individual grade range
@param[$grades] = array 
@return array
*/
function getGradeDistribution( $grades ) {
    #check if it parameter passed is not an array
	if ( !is_array( $grades ) ) {
        echo '<p>Invalid parammeter to getGradeDistribution() function: ' . $grades . ' is not an array</p>';
        return FALSE;
    }

	#initialize array designated to store grade classification
    $distribution_array = array();
    $distribution_array['Dist'] = 0;
    $distribution_array['Merit'] = 0;
    $distribution_array['Pass'] = 0;
    $distribution_array['Fail'] = 0;
	#iterate through each grade element and distribute it accordingly to the mark range 
    foreach ( $grades as $grade ) {
        if ( $grade >= 70 ) {
            $distribution_array['Dist']++;
        } else if ( $grade >= 60 ) {
            $distribution_array['Merit']++;
        } else if ( $grade >= 40 ) {
            $distribution_array['Pass']++;
        } else {
            $distribution_array['Fail']++;
        }
    }
    return $distribution_array;
}

/*
function returns the error count based on array of validated data(marks or header)
@param[$validatedData] = array 
@return integer
*/
function getErrors( $validatedData ) {
	#check if it parameter passed is not an array
    if ( !is_array( $validatedData ) ) {
        echo '<p>Invalid parammeter to getErrors() function: ' . $validatedData . ' is not an array</p>';
        return FALSE;
    }
    $errors = 0;
	#for each invalid element the error count will be incremented by one
    foreach ( $validatedData as $element ) {
        if ( $element['isValid'] === FALSE ) {
            $errors++;
        }
    }
    return $errors;
}

/*
function used to check if a word is a whole number or not, returns true if it is true and false otherwise
@param[$word] = string 
@return boolean
*/
function isWholeNumber( $word ) {
    $wordLength = strlen( $word );
    if ( $wordLength === 0 ) return false;
    #check individualy each word element if it is a digit, if only character is not digit return false
    $digitsList = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '0' );
    for ( $i = 0; $i<$wordLength; $i++ ) {
        if ( !in_array( $word[$i], $digitsList ) ) {
            return FALSE;
        }
    }
    return TRUE;
}

function displayCoursesData($directoryName) {
	
	#initialize the array that contains readable txt files paths
    $txtFiles = getTxtFilesPath( FOLDER_NAME );
	
	#iterate through each txt file and display data accordingly
    foreach ( $txtFiles as $file ) {
		#HEADER
		echo "<br><h3> Module Header Data... </h3>";
	    #initialize and display fileName
        $fileName = getFileName( $file );
        echo "<p>File name : $fileName <p>";
        
		#store all data from a file into an array
        $courseData = getRawFileData( $file );
		
		#store validated header details into an array
        $header = getValidatedHeader( $courseData );
		
		#display validated header details
		if($header!=FALSE){
			foreach ($header as $key => $headerElement){
				echo '<p>'.$key. ' : '. $headerElement['value']. $headerElement['errorType'].  '</p>';
			}
		}
		
        #MARKS 
		#store validated marks details into an array
        $marks = getValidatedMarks( $courseData );
		if($marks!=FALSE){
			#display student id and mark data read from file...
			echo "<h3> Student ID and Mark data read from file... </h3>";
			foreach ($marks as $marksElement){
				echo '<p>'.$marksElement['id']. ' : '. $marksElement['grade']. $marksElement['errorType']. '</p>';				
			}
		
			#display id's and module marks to be included...
			echo "<h3> Student ID and Mark data read from file... </h3>";
			foreach ($marks as $marksElement){
				if($marksElement['isValid']){
					echo '<p>'.$marksElement['id']. ' : '. $marksElement['grade'] . '</p>';			
				}			
			}
		}
	
        $grades = getValidGrades( $marks );
		//ANALYSIS
		echo "<h3> Statistical Analysis of module marks... </h3>";
        $gradeAnalysis = getModuleAnalysis( $grades );
		if($grades!=FALSE){
			foreach ($gradeAnalysis as $key => $element){
				echo '<p>'. $key . ' : '. $element . '</p>';					
			}
			#GRADE NUMBER
			$gradesNumber=count($grades);
			echo( "<p># of students : $gradesNumber <p>" );
			
		}

        #ERROR HEADER
        $errorsHeader = getErrors( $header );
		echo( "<p># of Header Errors : $errorsHeader <p>" );
        #ERROR MARKS
        $errorsMarks = getErrors( $marks );
		echo( "<p># of Student data Errors : $errorsMarks <p>" );


        #GRADE DISTRIBUTION
		echo "<h3> Grade Distribution of module marks... </h3>";
		$gradeDistribution = getGradeDistribution( $grades );
		if($gradeDistribution!=FALSE){
			foreach ($gradeDistribution as $key => $element){
					echo '<p>'. $key . ' : '. $element . '</p>';					
			}
			#line
			echo '<hr>';	
			}
	}
}

define( "FOLDER_NAME", "files" );
displayCoursesData(FOLDER_NAME);


?>

