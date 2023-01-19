<?php /** @noinspection UseStaticReturnTypeInsteadOfSelfInspection */
/*
Завдання: необхідно написати клас для пошуку файлів і директорій.
Клас має реалізовувати інтерфейс FileFinderInterface.
На виході клас має повертати масив строк - повних шляхів до папок/файлів, які відповідають заданим умовам.
Нижче в файлі є приклади використання класу.

Можна використовувати тільки вбудований функціонал PHP.

Завдання розраховане на 1-2 години роботи, просимо не витрачати більше.
Краще додайте до реалізації список доробок/покращень, які ви б зробили в коді, якби працювали б над ним далі.
*/

interface FileFinderInterface
{

    /**
     * Search in directory $directory.
     * If called multiple times, the result must include paths from all provided directories.
     */
    public function inDir (string $directory) : self;

    /** Filter: only files, ignore directories */
    public function onlyFiles () : self;

    /** Filter: only directories, ignore files */
    public function onlyDirectories () : self;

    /**
     * Filter by regular expression on full path.
     * If called multiple times, the result must include paths that match at least one of the provided expressions.
     */
    public function match (string $regularExpression) : self;


    /**
     * Returns array of all found files/directories (full path)
     * @return string[]
     */
    public function find () : array;

}


/** @noinspection PhpHierarchyChecksInspection */
class FileFinderImplementation implements FileFinderInterface
{

    private $listArray;
    private $dirArray;
    private $matchArray;
    private $isFile;
    private $isDir;

    const excludeArr = array (".", "..");

    function __construct ()
    {
        $this->listArray  = array ();
        $this->dirArray   = array ();
        $this->matchArray = array ();
        $this->isFile     = FALSE;
        $this->isDir      = FALSE;
    }


    function inDir (string $directory) : self
    {
        $this->dirArray [] = $directory;

        return $this;
    }

    function onlyFiles () : self
    {
        $this->isFile = TRUE;

        return $this;
    }

    function onlyDirectories () : self
    {
        $this->isDir = TRUE;

        return $this;
    }

    function match (string $regularExpression) : self
    {
        $this->matchArray [] = $regularExpression;

        return $this;
    }

    function find () : array
    {

        foreach ($this->dirArray as $dir) {

            $entries = scandir ($dir);
            foreach ($entries as $entry) {

                $fullPath = realpath ($dir.$entry);

                if (!in_array ($entry, self::excludeArr))
                    if (!$this->isFile && !$this->isDir) {
                        $this->listArray[] = $fullPath;
                    } else {
                        if ($this->isFile && is_file ($fullPath))
                            $this->listArray[] = $fullPath;
                        if ($this->isDir && is_dir ($fullPath))
                            $this->listArray[] = $fullPath;
                    }

            }

        }

        if (!empty($this->matchArray)) {
            $tempMatchArr = array ();
            foreach ($this->listArray as $key => $item) {
                foreach ($this->matchArray as $match) {
                    if (preg_match ($match, $item)) {
                        $tempMatchArr [] = $this->listArray[ $key ];

                    }
                }

            }

            unset($this->listArray);
            $this->listArray = $tempMatchArr;
        }


        return $this->listArray;
    }

}


$finder = new FileFinderImplementation();

# search for all .php or .tmp files in directories ./ and folder1/
$finder
    ->inDir ('./')
    ->inDir ('folder1/')
    ->match ('/.*\.php$/')
    ->match ('/.*\.tmp$/')
    ->onlyFiles ();
foreach ($finder->find () as $file) {
    print $file."<br/>";
}
print "<br/><br/>";


# search for all files in ./
$finder = new FileFinderImplementation();

$finder
    ->inDir ('./')
    ->onlyFiles ();
foreach ($finder->find () as $file) {
    print $file."<br/>";
}
print "<br/><br/>";


# search for all files in folder1
$finder = new FileFinderImplementation();

$finder
    ->inDir ('folder1/')
    ->onlyDirectories ();
foreach ($finder->find () as $file) {
    print $file."<br/>";
}

