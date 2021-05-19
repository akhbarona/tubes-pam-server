<?php

error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, content-type, Authorization, Content-Type, token');
header('content-type: application/json; charset=utf-8');


//database local komputer
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db = "bukukas";

// remote database lama
// $host = "remotemysql.com";
// $user = "JCVuDJCuOy";
// $pass = "E16eNziX4I";
// $db = "JCVuDJCuOy";


// free database from remotemysql.com
$host = "remotemysql.com";
$user = "ZqtnS15994";
$pass = "5Q4GhgUTT9";
$db = "ZqtnS15994";

$connection = mysqli_connect($host,$user,$pass,$db);

if(!$connection) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$op = $_GET['op'];
switch ($op) {
    case 'showalluser': showalluser();break;
    case 'showdatauser': showdatauser();break;
    case 'showusergoogle': showusergoogle();break;
    case 'showdata': showdata();break;
    case 'showtotal': showtotal();break;
    case 'showincome': showincome();break;
    case 'showspending': showspending();break;
    case 'showdatapost': showdatapost();break;
    case 'login' : login();break;
    case 'create': create();break;
    case 'creategoogle': creategoogle();break;
    case 'createpost': createpost();break;
    case 'update': update(); break;
    case 'delete': delete();break;
    default: echo 'endpoint server bukukas';break;
}

function showalluser(){
    global $connection;
    $sql = "SELECT * FROM user order by id desc";
    $query = mysqli_query($connection,$sql);
    while($r = mysqli_fetch_array($query)){
        $result[] = array(
            'id' => $r['id'],
            'nama' => $r['name'],
            'email' => $r['email']
        );
    }
    $data['data']['result'] = $result;
    echo json_encode($data);
}

function showdatauser(){
    global $connection;
    $id_user = $_GET['id'];
    $sql = "SELECT * FROM user WHERE id ='$id_user'";
    $query = mysqli_query($connection,$sql);
    while($r = mysqli_fetch_array($query)){
        $result[] = array(
            'id' => $r['id'],
            'nama' => $r['name'],
            'email' => $r['email'],
            'datetime' => date('d/m/Y', strtotime($r['datetime']))
        );
    }
    $data['data']['result'] = $result;
    echo json_encode($data);
}

function showusergoogle(){
    global $connection;
    $email = $_GET['email'];
    $sql = "SELECT * FROM user WHERE email ='$email'";
    $query = mysqli_query($connection,$sql);
    while($r = mysqli_fetch_array($query)){
        $result[] = array(
            'id' => $r['id'],
            'nama' => $r['name'],
            'email' => $r['email'],
            'datetime' => date('d/m/Y', strtotime($r['datetime']))
        );
    }
    $data['data']['result'] = $result;
    echo json_encode($data);
}

function showdata(){
    global $connection;
    $id_user = $_GET['id_user'];
    $sql = "SELECT id,id_user,CONCAT('Rp ', FORMAT(income,'id_ID')) AS income, CONCAT('Rp ', FORMAT(spending,'id_ID')) AS spending, note ,datetime FROM post WHERE id_user= '$id_user' ORDER BY id ASC";
    $query = mysqli_query($connection,$sql);
    if(mysqli_num_rows($query)==0){
        echo json_encode('belum ada data yang ditambahkan');
    }else{
        while ($r = mysqli_fetch_array($query)) {
            $result[] = array(
                'id' => $r['id'],
                'id_user' => $r['id_user'],
                'income' => $r['income'],
                'spending' => $r['spending'],
                'note' => $r['note'],
                'datetime' =>  date('d/m/Y', strtotime($r['datetime']))
            );
        }
        $data['data']['result'] = $result;
        echo json_encode($data);
    }
}

function showtotal(){
    global $connection;
    $id_user = $_GET['id_user'];
	$sql1 = "SELECT * FROM post WHERE id_user='$id_user'";
	$query1 = mysqli_query($connection,$sql1);
    $sql2 = "SELECT CONCAT('Rp ', FORMAT((SUM(income)-SUM(spending)),'id_ID')) as total FROM post WHERE id_user='$id_user'";
    $query2 = mysqli_query($connection,$sql2);
	 if(mysqli_num_rows($query1)>0){
        while($r = mysqli_fetch_array($query2)){
				$result[] = array(
					'total' => $r['total']
			);
        }
		$data['data']['result'] = $result;
        echo json_encode($data);
    }else{
            echo json_encode('data tidak ada');
    }

}

function showincome(){
    global $connection;
    $id_user = $_GET['id_user'];
    $sql1 = "SELECT * FROM post WHERE id_user='$id_user'";
	$query1 = mysqli_query($connection,$sql1);
    $sql2 = "SELECT CONCAT('Rp ', FORMAT(SUM(income),'id_ID')) as pendapatan FROM post WHERE id_user='$id_user'";
    $query2 = mysqli_query($connection,$sql2);
    if(mysqli_num_rows($query1)>0){
        while($r = mysqli_fetch_array($query2)){
            $result[] = array(
                'income' => $r['pendapatan']
            );
        }
        $data['data']['result'] = $result;
        echo json_encode($data);
   }else{
           echo json_encode('data tidak ada');
   }
}

function showspending(){
    global $connection;
    $id_user = $_GET['id_user'];
	$sql1 = "SELECT * FROM post WHERE id_user='$id_user'";
	$query1 = mysqli_query($connection,$sql1);
    $sql2 = "SELECT CONCAT('Rp ', FORMAT(SUM(spending),'id_ID')) as pengeluaran FROM post WHERE id_user='$id_user'";
    $query2 = mysqli_query($connection,$sql2);
	 if(mysqli_num_rows($query1)>0){
         while($r = mysqli_fetch_array($query2)){
			$result[] = array(
            'spending' => $r['pengeluaran']
			);
		}
		$data['data']['result'] = $result;
		echo json_encode($data);
    }else{
            echo json_encode('data tidak ada');
    }
}


function showdatapost()
{
    global $connection;
    $id_post = $_GET['id'];
    $id_user = $_GET['id_user'];
    $sql = "SELECT * FROM post  WHERE id='$id_post' AND id_user='$id_user'";
    $query = mysqli_query($connection,$sql);
    while($r = mysqli_fetch_array($query)){
        $result[] = array(
            'income' => $r['income'],
            'spending' => $r['spending'],
            'note' => $r['note'],
        );
    }
    $data['data']['result'] = $result;
    echo json_encode($data);
}




function login(){
    global $connection;
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $email = $obj['email'];
    $password = $obj['password'];
    $sql1 = "SELECT * FROM user where email='$email'";
    $query1= mysqli_query($connection,$sql1);
    if(mysqli_num_rows($query1)>0){
        $q = mysqli_fetch_array($query1);
        $hashpassword = $q['password'];
        $cek = password_verify($password,$hashpassword);
        if($cek){
            $sql2 = "SELECT * FROM user where email='$email' and password='$hashpassword'";
            $query2= mysqli_query($connection,$sql2);
              while($r = mysqli_fetch_array($query2)){
                $result[] = array(
                    'id' => $r['id'],
                    'name' => $r['name'],
                    'email' => $r['email'],
                    'datetime' => $r['datetime'],
                );
            }
            $data['data']['result'] = $result;
            echo json_encode($data);
        }else{
            echo json_encode('wrong password');
        }
    }else{
        echo json_encode('email not registered');
    }
}

function create(){
    global $connection;
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $nama = $obj['name'];
    $email = $obj['email'];
    $password = $obj['password'];
    $hashpassword = password_hash($password, PASSWORD_DEFAULT);

    if($obj['email']!=""){
        $sql1 = "SELECT * FROM user WHERE email='$email'";
        $query1 = mysqli_query($connection,$sql1);
        if(mysqli_num_rows($query1) > 0){
            echo json_encode("email already exist");
        }else{
            $sql2 = "INSERT INTO user(name,email,password) VALUES ('$nama','$email','$hashpassword')";
            $query2 = mysqli_query($connection,$sql2);
            if($query2){
                $hasil = "Success";
            }else{
                $hasil = "Gagal";
            }
            $data['data']['result'] = $hasil;
            echo json_encode($data);
        }
    }else{
        echo json_encode('try again');
    }
}


function creategoogle(){
    global $connection;
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $nama = $obj['name'];
    $email = $obj['email'];

    if($obj['email']!=""){
        $sql1 = "SELECT * FROM user WHERE email='$email'";
        $query1 = mysqli_query($connection,$sql1);
        if(mysqli_num_rows($query1) > 0){
            echo json_encode("user and email already exist");
        }else{
            $sql2 = "INSERT INTO user(name,email) VALUES ('$nama','$email')";
            $query2 = mysqli_query($connection,$sql2);
            if($query2){
                $hasil = "User registered Successfully";
            }else{
                $hasil = "Gagal memasukkan data user";
            }
            $data['data']['result'] = $hasil;
            echo json_encode($data);
        }
    }else{
        echo json_encode('try again');
    }
}

function createpost(){
    global $connection;
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $id_user = $obj['id_user'];
    $income = $obj['income'];
    $spending = $obj['spending'];
    $note = $obj['note'];
    if($id_user and $income and $spending){
        $sql = "INSERT INTO post(id_user,income,spending,note) VALUES ('$id_user','$income','$spending','$note')";
        $query = mysqli_query($connection,$sql);
        if($query){
            $hasil = "Berhasil menambahkan data";
        }else{
            $hasil =  "Gagal menambahkan data";
        }
    }
    $data['data']['result'] = $hasil;
    echo json_encode($data);
}

function update(){
    global $connection;
    $json = file_get_contents('php://input');
    $obj = json_decode($json,true);
    $id = $_GET['id'];
    $id_user = $_GET['id_user'];
    $income = $obj['income'];
    $spending = $obj['spending'];
    $note = $obj['note'];
    if($income){
        $set[] = "income='$income'";
    }
    if($spending){
        $set[] = "spending='$spending'";
    }
    if($note){
        $set[] = "note='$note'";
    }
    $hasil = "Gagal melakukan update data";
    if($income or $spending or $note){
        $sql = "UPDATE post SET ".implode(",",$set).",datetime=now() WHERE id = '$id' AND id_user = '$id_user'";
        $query = mysqli_query($connection,$sql);
        if($query){
            $hasil = "Data berhasil diupdate";
        }
    }
    $data['data']['result'] = $hasil;
    echo json_encode($data);
}

function delete(){
    global $connection;
    $id = $_GET['id'];
    $id_user = $_GET['id_user'];
    $sql = "DELETE FROM post WHERE id ='$id' AND id_user ='$id_user'";
    $resetno = "ALTER TABLE post DROP id";
    $numbers = "ALTER TABLE post ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST";
    $query1 = mysqli_query($connection,$sql);
    $query2 = mysqli_query($connection,$resetno);
    $query3 = mysqli_query($connection,$numbers);
    if($query1 && $query2 && $query3){
        $hasil = "Berhasil menghapus data";
    }else{
        $hasil = "Gagal menghapus data";
    }
    $data['data']['result'] = $hasil;
    echo json_encode($data);
}


?>