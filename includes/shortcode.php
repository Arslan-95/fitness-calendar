<?php

add_shortcode( 'fitness_client_app', 'fitness_calendar_shortcode' );

function fitness_calendar_shortcode(){
  wp_enqueue_style('datepicker', plugins_url('/assets/datepicker/dist/css/datepicker.min.css', __FILE__));
  wp_enqueue_style('datepicker-bs4', plugins_url('/assets/datepicker/dist/css/datepicker-bs4.min.css', __FILE__));
  wp_enqueue_style('datepicker-bulma', plugins_url('/assets/datepicker/dist/css/datepicker-bulma.min.css', __FILE__));
  wp_enqueue_style('datepicker-foundation', plugins_url('/assets/datepicker/dist/css/datepicker-foundation.min.css', __FILE__));
  
  wp_enqueue_script('datepicker', plugins_url('/assets/datepicker/dist/js/datepicker.min.js', __FILE__));
  wp_enqueue_script('datepickerLang', plugins_url('/assets/datepicker/dist/js/locales/ru.js', __FILE__));
  wp_enqueue_script('front-end', plugins_url('/assets/js/front-end.js', __FILE__));

  $works = get_option('fitness_works');
  ?>
    <style>
      .fitness-calendar{
        width: 100%;
        display: flex;
        justify-content: space-between;
      }
      
      .fitness-calendar__date{
        height: 37px;
        resize: none !important;
        border: 2px solid #000;
        padding: 0 5px;
        box-sizing: border-box;
        border-radius: 5px;
      }
      .fitness-calendar__date::-webkit-calendar-picker-indicator{
        cursor: pointer;
      }

      .fitness-workout__container{
        display: flex;
        justify-content: space-between;
        max-width: 500px;
        width: 100%;
      }

      .fitness-calendar__column{
        width: 33.33%;
        display: flex;
        justify-content: center;
      }

      .fitness-workouts{
        flex-direction: column;
        justify-content: start;
        font-size: 14px;
      }

      .fitness-workout__container > span{
        width: 50%;
        text-align: center;
      }

      .fitness-workout__title{
        font-size: 12px;
        font-weight: 700;
      }

      .fitness-workout{
        width: 100%;
        cursor: pointer;
        margin-bottom: 10px;
        box-sizing: border-box;
        transition: all .2s;
      }

      .fitness-workout__time{
        font-weight: 600;
      }

      /* form */
      .fitness_form {
          max-width: 325px;
          width: 100%;
        }

      .fitness_form__item {
        margin: 10px 0;
      }

      .fitness_form__item p {
        margin: 4px 0;
        font-size: 18px;
        font-weight: 600;
      }

      .fitness_form__submit button {
        border: 0;
        font-size: 18px;
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 7px;
        font-size: 18px;
        text-align: center;
        background: linear-gradient(to bottom, #ed4036, #cf3931);
        color: #fff;
        transition: 0.4s ease all;
        width: 100%;
      }

      .fitness_form__submit {
        width: 99%;
        margin-top: 21px;
      }

      .fitness_form__item input {
        border-radius: 4px;
        border: 1px #000 solid;
        padding: 10px 3% !important;
        width: 100%;
        background: rgba(1, 1, 1, 0.05) !important;
        border: 1px solid transparent;
      }

      /* Workout Active */
      .fitness-workout.active {
        padding: 5px 8px;
        border-radius: 5px;
        background: linear-gradient(to bottom, #ed4036, #cf3931);
        color: #fff;
      }

      .fitness-workout.disabled {
        padding: 5px 8px;
        border-radius: 5px;
        opacity: .6;
        color: #000;
      }

      .fitness_form button{
        transition: .2s;
      }
      
      @media screen and (max-width: 1024px) {
        .fitness-calendar {
          flex-direction: column;
          align-items: center;
        }  
        
        .fitness-calendar__column {
          width: 100%;
          margin-bottom: 20px;
        }

        .fitness-workouts {
          align-items: center;
        }

        .fitness-workout {
          max-width: 350px;
        }

        .fitness_form__item p {
          font-size: 15px;
        }

        .fitness_form__submit button {
          display: block;
          font-size: 16px;
          width: 85%;
          margin: 0 auto;
        }

        .fitness_form__item input {
          padding: 5px 3% !important;
        }

        .fitness-workout {
          border-bottom: 1px solid rgba(1,1,1, .3);
          padding-bottom: 10px;
        }
        
        .fitness-workout.active {
          border: none;
        }
        
      }
         .popup__bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        pointer-events: none;
        transition: 0.5s all;
    }

    .popup__bg.active {
        opacity: 1;
        pointer-events: all;
        transition: 0.5s all;
    }

    .popup {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        background: #fff;
        width: 400px;
        padding: 25px;
        transition: 0.5s all;
        border-radius: 10px;
    }

    .popup.active {
        transform: translate(-50%, -50%) scale(1);
        transition: 0.5s all;
    }

    .popup h2 {
        font-size: 1.2rem;
        text-align: center;
    }

    .close-popup {
        cursor: pointer;
        font-size: 1.2rem;
        margin-top: 2rem;
        padding: 0.3rem 1rem;
        text-align: center;
        color: white;
        background: linear-gradient(to bottom, #ed4036, #cf3931);
        border-radius: 7px;
    }
    </style>
    <div class="fitness-calendar">
      <div class="fitness-calendar__column">
        <div id="fitness_inline"></div>
      </div>
      <div class="fitness-calendar__column fitness-workouts">
      </div>
      <div class="fitness-calendar__column">
        <form class="fitness_form">
          <div class="fitness_form__item">
            <p>Ваши имя и фамилия</p>
            <input type="name" name="name" required>
          </div>
          <div class="fitness_form__item">
            <p>Номер телефона</p>
            <input type="tel" class="tel" name="tel" required>
          </div>
          <input type="hidden" name="id" value="null">
          <input type="hidden" name="fitness-workout-form" value="true">
          <div class="fitness_form__submit">
            <button type="submit">
              Записаться на тренировку
            </button>
          </div>
        </form>
      </div>
    </div>
    <script>
      const fitness_data = <?php echo json_encode($works); ?>;
    </script>
  <?php
}