# YoPrint Laravel Coding Project Spec

- clone the project
- run `composer install`
- create the database, `yoprint_laravel_coding_project_spec`
- run `php artisan migrate`
- run `php artisan serve`
- run `php artisan horizon`
- run `php artisan queue-work --timeout=0`

# Questions

Q: Why do you have multiple branches named solution-#?

A: I decided to test different solutions to see which one would provide a better optimized outcome. Solutions 2-5 I decided to try and implement something new I learn from https://youtu.be/CAi4WEKOT4A?si=-iAdQ6vYuYi0paFY

Solution-1: My own solution with minimal help using chatGPT. Works but not the best at it can be quite slow, takes about 5 minutes to process the initial file.
Solution-2: Implement chunking which significantly increase the processing speed.
