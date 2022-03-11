<?php

add_action('init', 'fitness_work');

function fitness_work(){
  if(!isset($_POST['fitness-page'])){
    return;
  }

  if(isset($_POST['add_fitness_work'])){
    $works = get_option('fitness_works');
    $count = uniqid();
    
    $works[$count] = [
      'id' => $count,
      'title' => $_POST['title'],
      'date' => $_POST['date'],
      'time' => $_POST['time'],
      'number' => $_POST['number'],
      'users' => [],
    ];

    update_option('fitness_works', $works);   
  }else if(isset($_POST['delete_fitness_work'])){
    $works = get_option('fitness_works');

    unset($works[$_POST['delete_fitness_work']]);

    return update_option('fitness_works', $works);
  }else if(isset($_POST['delete_fitness_user'])){
    $works = get_option('fitness_works');
    $workId = $_POST['work_id'];
    $userId = $_POST['delete_fitness_user'];

    unset($works[$workId]['users'][$userId]);
    $works[$workId]['number'] += 1;
    update_option('fitness_works', $works);
  }
}

add_action('wp_ajax_fitness_new_order', 'fitness_new_order');
add_action('wp_ajax_nopriv_fitness_new_order', 'fitness_new_order');

function fitness_new_order(){
  if(isset($_POST['fitness-workout-form'])){
    $id = $_POST['id'];
    $name = $_POST['name'];
    $tel = $_POST['tel'];

    $works = get_option('fitness_works');
    
    $works[$id]['users'][uniqid()] = [
      'name' => $name,
      'tel' => $tel
    ];
    
    $workNumber = $works[$id]['number'];
    $workNumber = intval($workNumber);
    $workNumber = $workNumber - 1;
    if($workNumber < 0){
      http_response_code(500);
      return;
    }
    $works[$id]['number'] = $workNumber;

    // Destruct Object Works.
    $workTitle = $works[$id]['title'];
    $workDate = $works[$id]['date'];
    $workTime = $works[$id]['time'];
    
    // Mail.
    $to = 'my@mail.com';
    $subject = 'Запись на занятия';
    $headers = 'From: My Name <myname@mydomain.com>' . "\r\n";
    $message = "
      $name Записался на занятие:
      Занятие: $workTitle
      Дата и Время:  $workDate  $workTime
      Номер телефона: $tel
    ";
    
    // Use Changes.
    wp_mail( $to, $subject, $message, $headers );        
    update_option('fitness_works', $works);
  }
}