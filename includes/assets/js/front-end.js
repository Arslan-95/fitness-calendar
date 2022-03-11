document.addEventListener('DOMContentLoaded', () => {
  const elem = document.querySelector('#fitness_inline');
  const datepicker = new Datepicker(elem, {
    minDate: new Date(),
    language: 'ru',
  });

  datepicker.setDate(new Date());
  datepicker.element.addEventListener('changeDate', () => {
    dataWhile(fitness_data);
    setNextTime();
  });

  // After Content Loaded Use Functions
  dataWhile(fitness_data);
  setNextTime();
  // Tel mask
  [].forEach.call(document.querySelectorAll('.tel'), function (input) {
    var keyCode;
    function mask(event) {
      event.keyCode && (keyCode = event.keyCode);
      var pos = this.selectionStart;
      if (pos < 3) event.preventDefault();
      var matrix = '+7 (___) ___ ____',
        i = 0,
        def = matrix.replace(/\D/g, ''),
        val = this.value.replace(/\D/g, ''),
        new_value = matrix.replace(/[_\d]/g, function (a) {
          return i < val.length ? val.charAt(i++) || def.charAt(i) : a;
        });
      i = new_value.indexOf('_');
      if (i != -1) {
        i < 5 && (i = 3);
        new_value = new_value.slice(0, i);
      }
      var reg = matrix
        .substr(0, this.value.length)
        .replace(/_+/g, function (a) {
          return '\\d{1,' + a.length + '}';
        })
        .replace(/[+()]/g, '\\$&');
      reg = new RegExp('^' + reg + '$');
      if (
        !reg.test(this.value) ||
        this.value.length < 5 ||
        (keyCode > 47 && keyCode < 58)
      )
        this.value = new_value;
      if (event.type == 'blur' && this.value.length < 5) this.value = '';
    }

    input.addEventListener('input', mask, false);
    input.addEventListener('focus', mask, false);
    input.addEventListener('blur', mask, false);
    input.addEventListener('keydown', mask, false);
  });

  const form = document.querySelector('.fitness_form');
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const button = document.querySelector('.fitness_form button');

    const formData = new FormData(event.currentTarget);
    formData.append('action', 'fitness_new_order');

    if (formData.get('id') !== 'null') {
      button.disabled = true;
      button.style.opacity = '.6';

      fetch('/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData,
      })
        .then(() => {
          document.querySelector('.popup__bg').classList.add('active');
          document.querySelector('.popup').classList.add('active');
          const id = formData.get('id');

          fitness_data[id]['number'] = fitness_data[id]['number'] - 1;
          dataWhile(fitness_data);
          setNextTime();

          button.disabled = false;
          button.style.opacity = '1';
        })
        .catch((error) => {
          console.log(error);
        });
    } else {
      alert('Пожалуйста, выберите занятие.');
    }
  });

  function dataWhile(fitness_data) {
    const workouts = document.querySelector('.fitness-workouts');
    workouts.innerHTML = '';

    let isWorkouts = false;

    setInputId(); // Clean Input Id

    for (const key of Object.keys(fitness_data)) {
      const choosedDate = datepicker.getDate('yyyy-mm-dd');
      const item = fitness_data[key];
      const { title, date, time, number } = item;

      if (choosedDate !== date) {
        continue;
      }

      isWorkouts = true;

      const workout = createWorkoutElement(title, date, time, number, key);
      if (number >= 1) {
        workout.addEventListener('click', (event) => {
          toggleWorkoutClass(event);
          setInputId(key); // Set Input Id
        });
      } else {
        workout.classList.add('disabled');
      }
    }

    if (isWorkouts === false) {
      const span = document.createElement('span');
      span.innerHTML = 'Нет занятий в этот день.';
      span.style.textAlign = 'center';
      span.style.marginTop = '10px';
      workouts.append(span);
    }
  }

  function toggleWorkoutClass(event) {
    const workouts = document.querySelectorAll('.fitness-workout');
    workouts.forEach((item) => item.classList.remove('active'));
    const workout = event.currentTarget;
    workout.classList.add('active');
  }

  function createWorkoutElement(title, date, time, number, id) {
    const workouts = document.querySelector('.fitness-workouts');

    const workout = addElement('div', 'fitness-workout');
    const workout__title = addElement('div', 'fitness-workout__title', title);
    const workout__container = addElement('div', 'fitness-workout__container');
    const workout__time = addElement('span', 'fitness-workout__time', time);
    const workout__number_container = addElement(
      'span',
      'fitness-workout__number-container'
    );
    const workout__number_text = addElement(
      'span',
      'fitness-workout__number-text',
      setNumberText(number)
    );
    workout__number_text.id = 'workout-number-' + id;

    workout.append(workout__title);
    workout.append(workout__container);
    workout__container.append(workout__time);
    workout__container.append(workout__number_container);
    workout__number_container.append(workout__number_text);
    workouts.append(workout);

    return workout;
  }

  function addElement(tag, classList, text = '') {
    const element = document.createElement(tag);
    element.classList = classList;
    element.innerHTML = text;
    return element;
  }

  function setNumberText(number) {
    if (number === 1) {
      return number + ' свободное место';
    } else if (number > 1 && number <= 4) {
      return number + ' свободных места';
    } else if (number >= 5) {
      return number + ' свободных мест';
    } else {
      return 'нет мест';
    }
  }

  function setNextTime() {
    const times = document.querySelectorAll('.fitness-workout__time');
    times.forEach((time) => {
      const timeHtml = time.innerHTML;
      let nextTime = timeHtml.substr(0, 2);

      let finalNumber = Number(nextTime) + 1;

      if (finalNumber >= 24) {
        finalNumber = 0;
      }

      let newNumber = '0' + finalNumber;
      newNumber = newNumber.toString().slice(-2);
      nextTime = timeHtml.replace(nextTime, newNumber);

      time.innerHTML = `с ${timeHtml} до ${nextTime}`;
    });
  }
});

function setInputId(id = 'null') {
  const inputId = document.querySelector('.fitness_form input[name="id"]');
  inputId.value = id;
}

let div = document.createElement('div');
div.className = 'alert';
div.innerHTML = `
  <div class="popup__bg">
  <div class="popup">
      <h2>Вы успешно записаны на тренировку</h2>
  <div class="close-popup">Закрыть</div>
  </div> 
  </div>  
`;
document.body.append(div);

let popupBg = document.querySelector('.popup__bg');
let popup = document.querySelector('.popup');
let closePopupButton = document.querySelector('.close-popup');

closePopupButton.addEventListener('click', () => {
  popupBg.classList.remove('active');
  popup.classList.remove('active');
});

document.addEventListener('click', (e) => {
  if (e.target === popupBg) {
    popupBg.classList.remove('active');
    popup.classList.remove('active');
  }
});
