<?php

add_action('admin_menu', 'register_fitness_calendar_page');

function register_fitness_calendar_page() {
  add_menu_page(
		'Fitness Calendar',
		'Fitness Calendar',
		'edit_others_posts',
    'fitness_calendar_page',
    'fitness_calendar_page_display'
	);
}

function fitness_calendar_page_display(){
  ?>
    <style>
      .form_add{
        display:flex;
        flex-direction:column;
        align-items: start;
      }
      .form_add > label{
        display: flex;
        align-items: center;
        margin-bottom: 5px;
      }
      .form_add > label strong{
        width: 120px;
      }
      
      .btn{
        border: none;
        background-color: #154BA0;
        color: #fff;
        padding: 6px 8px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
      }

      // Table      
      .works th {
        padding: 0 0;
      }

      .works .work td {
        padding: 6px 25px;
        text-align: center;
      }

      .work__delete button{
        border: none;
        cursor: pointer;
      }
      
      .work__delete button:hover{
        opacity: 0.8;
      }

      table {
        border-collapse: collapse;
      }
      
      tr:nth-of-type(odd) {
          background: #eee;
      }

      th {
          background: rgb(238, 238, 238);
          color: rgb(46, 46, 46);
          font-weight: bold;
      }

      td,
      th {
          padding: 6px;
          border: 1px solid rgb(129, 129, 129);
          text-align: center;
      }

      .works{
        margin-top: 30px;
      }

      .work__title,
      .work__date,
      .work__time,
      .work__number{
        font-weight: 600;
      }

      strong{
        font-weight: 500;
        opacity: .9;
      }
    </style>
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form class="form_add" method="post" action="<?php the_permalink(); ?>">
      <label>
        <strong>Название занятия</strong>
        <input type="text" name="title" placeholder="Название занятия">
      </label>
      <label>
        <strong>Дата</strong>
        <input type="date" name="date" required>
      </label>
      <label>
        <strong>Начало занятий</strong>
        <input type="time" name="time" required>
      </label>
      <label>
        <strong>Количество мест</strong>
        <input type="number" name="number" placeholder="Количество мест">
      </label>
      <input type="hidden" name="add_fitness_work">
      <input type="hidden" name="fitness-page">
      <button type="submit" class="btn">Добавить</button>
    </form>
    <table class="works">
      <thead>
        <tr>
          <th>Название</th>
          <th>Дата</th>
          <th>Время</th>
          <th>Количество мест</th>
          <th>Удалить</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $works_list = get_option('fitness_works');
          
          foreach($works_list as $key => $work){
            ?>
              <tr class="work">
                <td class="work__title"><?php echo $work['title']; ?></td>
                <td class="work__date"><?php echo $work['date']; ?></td>
                <td class="work__time"><?php echo $work['time']; ?></td>
                <td class="work__number"><?php echo $work['number']; ?></td>
                <td class="work__delete">
                  <form action="<?php the_permalink(); ?>" method="post">
                    <input type="hidden" name="delete_fitness_work" value="<?php echo $work['id']; ?>">
                    <input type="hidden" name="fitness-page">
                    <button type="submit">✖</button>
                  </form>
                </td>
              </tr>

              <?php
                $users = [];

                if(isset($work['users'])){
                  $users = $work['users'];
                }
                if(isset($users) && is_array($users) && count($users) > 0){
                  $i = 0;
                  foreach($users as $userKey => $user){
                    ?>
                      <tr class="work">
                        <td><?php echo ++$i; ?></td>
                        <td><strong>Имя:</strong> <?php echo $user['name']; ?></td>
                        <td><strong>Номер:</strong> <?php echo $user['tel']; ?></td>
                        <td></td>
                        <td class="work__delete">
                          <form action="<?php the_permalink(); ?>" method="post">
                            <input type="hidden" name="delete_fitness_user" value="<?php echo $userKey; ?>">
                            <input type="hidden" name="work_id" value="<?php echo $work['id']; ?>">
                            <input type="hidden" name="fitness-page">
                            <button type="submit">✖</button>
                          </form>
                        </td>
                      </tr>
                    <?php
                  }
                }
              ?>
            <?php
          }
        ?>
      </tbody>
    </table>

    <script>
      const times = document.querySelectorAll('.work__time');
      times.forEach((time) => {
        const timeHtml = time.innerHTML;
        let nextTime = timeHtml.substr(0, 2);;
        
        let finalNumber = Number(nextTime) + 1;

        if (finalNumber >= 24) {
          finalNumber = 0;
        }

        let newNumber = '0' + finalNumber
        newNumber = newNumber.toString().slice(-2);
        nextTime = timeHtml.replace(nextTime, newNumber);

        time.innerHTML = timeHtml + '-' + nextTime;
      });
    </script>
  <?php
}