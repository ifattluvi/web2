<?php

class Animal {
    public $animals;

    public function __construct($ar_animal)
    {
        $this->animals = $ar_animal;
    }

    public function index(){
        foreach ($this->animals as $animal){
            echo "- $animal <br/>";
        }
    }

    public function store($animal) {
        $this->animals[] = $animal;
    }

    public function update($index, $animal) {
        $this->animals[$index] = $animal;
    }

    public function destroy($index) {
        unset($this->animals[$index]);
    }
}

# membuat objek
# kirimkan data array ke dalam constructor
$animal = new Animal(["Ayam", "Ikan"]);

echo "Index - Menampilkan seluruh hewan <br/>";
$animal->index();
echo "<br/>";

# Method Store
echo "Store - Menambahkan hewan baru (burung) <br/>";
$animal->store("Burung");
$animal->index();
echo "<br/>";

# Method Update
echo "Update - Mengupdate hewan <br/>";
$animal->update(0, "Kucing Anggora");
$animal->index();
echo "<br/>";

echo "Destroy - Menghapus hewan <br/>";
$animal->destroy(1);
$animal->index();
echo "<br/>";