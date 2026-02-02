# FINKI Roadmap

## Опис
**FINKI Roadmap** е веб апликација наменета како водич за студентите на Факултетот за информатички науки и компјутерско инженерство (ФИНКИ).  
Проектот има за цел да обезбеди структуриран преглед на студиите, предметите и препорачаните технологии за академски и професионален развој.

Апликацијата е изработена со **Laravel** како backend framework и **Tailwind CSS** за frontend стилови.

---

## Користени технологии
- Laravel (PHP)
- Tailwind CSS
- Blade Templates
- MySQL / SQLite
- Javascript
- Vite

---

## Предуслови
Потребно е да бидат инсталирани:
- PHP 8.x
- Composer
- Node.js и NPM
- MySQL или SQLite
- Git

---

## Инсталација и стартување

### 1. Клонирање на проектот
`git clone https://github.com/DarkoVanevski/finki_roadmap.git`  
`cd finki_roadmap`

---

### 2. Инсталација на PHP зависности
`composer install`

---

### 3. Инсталација на frontend зависности
`npm install`

---

### 4. Конфигурација на околина
Креирање на `.env` фајл:  
`cp .env.example .env`

Генерирање на апликациски клуч:  
`php artisan key:generate`

Подесете ги параметрите за базата на податоци во `.env`.

---

### 5. Миграции и иницијализација на податоци
`php artisan migrate`  
`php artisan db:seed`

---

### 6. Стартување на апликацијата
Во еден терминал:  
`npm run dev`

Во друг терминал:  
`php artisan serve`

Апликацијата ќе биде достапна на:  
`http://127.0.0.1:8000`

---

## Автори
**Darko**  
**Hari** 

