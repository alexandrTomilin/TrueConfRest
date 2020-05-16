<?php
// app/Controller/UserController.php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as Render;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController
{
    private $render;

    public $pathJson;

    public $users;

    public function __construct(Render $render)
    {
        $this->render = $render;

        $this->pathJson = "./public/json/users.json";

        $this->users = $this->parseJson();
    }

    /* получаем json  преобразуем его в массив обьектов */
    public function parseJson()
    {
        $json = file_get_contents($this->pathJson);

        $json = json_decode($json, true);

        return $json;
    }

    /* получаем индекс по id */
    public function getUserIndex($id)
    {
        $index = null;

        foreach ($this->users as $key => $user)
        {
            if($user['id'] == $id)
            {
                $index = $key;
            }
        }

        return $index;
    }

    /* получение пользователя по id */
    public function find(ServerRequestInterface $request, ResponseInterface $response)
    {
        $id = $request->getAttribute('id');

        if(array_key_exists($this->getUserIndex($id), $this->users))
        {
            $user = json_encode($this->users[$this->getUserIndex($id)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $response->getBody()->write($user);
        } else
        {
            $response->getBody()->write($this->render->render('users/notFound.twig'));
        }

        return $response;
    }

    /* вывод списка пользователей */
    public function show(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write($this->render->render('users/out.twig', ['users' => $this->users]));

        return $response;
    }

    /* добавление пользователя по имени */
    public function add(ServerRequestInterface $request, ResponseInterface $response)
    {
        $json = $request->getParsedBody();

        $json = $json['json'];

        $json = json_decode($json, true);

        $i = 0;

        foreach ($this->users as $user)
        {
            $i++;
        }

        $newUser = ['id' => $i+1, 'username' => $json['name']];

        $this->users[] = $newUser;

        file_put_contents($this->pathJson, json_encode($this->users,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $response->getBody()->write('Вы добавили пользователя с именем: ' . $json['name']);

        return $response;
    }

    /* удаление пользователя по id из json файлв */
    public function delete(ServerRequestInterface $request, ResponseInterface $response)
    {
        $id = $request->getAttribute('id');

        foreach ($this->users as $key => $value)
        {
            if($value['id'] == $id)
            {
                unset($this->users[$key]);
            }
        }

        file_put_contents($this->pathJson, json_encode($this->users,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $response->getBody()->write('Вы удалили пользователя с id: ' . $id);

        return $response;
    }

    /* обновление имени пользователя по id в json файле */
    public function rename(ServerRequestInterface $request, ResponseInterface $response)
    {
        $name = $request->getParsedBody();

        $name = $name['name'];

        $name = json_decode($name, true);

        $name = $name['name'];

        $id = (int)$request->getAttribute('id');

        $this->users[$id - 1] = ['id' => $id, 'username' => $name];

        file_put_contents($this->pathJson, json_encode($this->users,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $response->getBody()->write('Вы изменили имя пользователя с id: ' . $id);

        return $response;
    }
}