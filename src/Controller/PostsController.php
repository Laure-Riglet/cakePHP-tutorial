<?php
// src/Controller/PostsController.php

namespace App\Controller;

class PostsController extends AppController
{
    public function index()
    {
        $posts = $this->Posts->find();
        return $this
            ->response
            ->withType("application/json")
            ->withStringBody(json_encode($posts));
    }

    public function view()
    {
        $id = $this->request->getParam('id');
        $post = $this->Posts
            ->findById($id)
            ->firstOrFail();
        return $this
            ->response
            ->withType("application/json")
            ->withStringBody(json_encode($post));
    }

    public function add()
    {
        $post = $this->Posts->newEmptyEntity();

        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());

            // Hardcoding the user_id is temporary, and will be removed later
            // when we build authentication out.
            $post->user_id = 1;

            if ($this->Posts->save($post)) {
                return $this
                    ->response
                    ->withType("application/json")
                    ->withStatus(201)
                    ->withStringBody(json_encode(['success' => 'The ' . $post->title . ' post has been created.', 'post' => $post]));
            }

            return $this
                ->response
                ->withType("application/json")
                ->withStatus(400)
                ->withStringBody(json_encode(['error' => $post->getErrors()]));
        }
        return $this
            ->response
            ->withType("application/json")
            ->withStatus(400)
            ->withStringBody(json_encode(['error' => 'undefined error']));
    }

    public function edit()
    {
        $id = $this->request->getParam('id');

        $post = $this->Posts
            ->findById($id)
            ->firstOrFail();

        if ($this->request->is(['post', 'put'])) {
            $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                return $this
                    ->response
                    ->withType("application/json")
                    ->withStringBody(json_encode(['success' => 'The ' . $post->title . ' post has been updated.', 'post' => $post]));
            }
            return $this
                ->response
                ->withType("application/json")
                ->withStatus(400)
                ->withStringBody(json_encode(['error' => $post->getErrors()]));
        }
        return $this
            ->response
            ->withType("application/json")
            ->withStatus(400)
            ->withStringBody(json_encode(['error' => 'undefined error']));
    }

    public function delete()
    {
        $id = $this->request->getParam('id');

        $this->request->allowMethod(['delete']);

        $post = $this->Posts->findById($id)->firstOrFail();
        if ($this->Posts->delete($post)) {
            return $this
                ->response
                ->withType("application/json")
                ->withStringBody(json_encode(['success' => 'The ' . $post->title . ' post has been deleted.']));
        }
        return $this
            ->response
            ->withType("application/json")
            ->withStringBody(json_encode(['error' => 'undefined error']));
    }
}
