<?php

namespace App\Controllers\Api;

use App\Models\ClassModel;

class ClassesController extends BaseApiController
{
    public function index()
    {
        $model = model(ClassModel::class);

        return $this->respond([
            'data' => $model->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function show(int $id)
    {
        $model = model(ClassModel::class);
        $row   = $model->find($id);

        if ($row === null) {
            return $this->failNotFound('Class not found');
        }

        return $this->respond($row);
    }

    public function create()
    {
        $data = $this->getJsonBody();

        $rules = [
            'name'  => 'required|string|min_length[2]',
            'grade' => 'permit_empty|string|max_length[30]',
            'year'  => 'permit_empty|string|max_length[20]',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $model = model(ClassModel::class);
        $id    = $model->insert([
            'name'  => $data['name'],
            'grade' => $data['grade'] ?? null,
            'year'  => $data['year'] ?? null,
        ], true);

        return $this->respondCreated(['id' => $id]);
    }

    public function update(int $id)
    {
        $data  = $this->getJsonBody();
        $model = model(ClassModel::class);

        if ($model->find($id) === null) {
            return $this->failNotFound('Class not found');
        }

        $rules = [
            'name'  => 'required|string|min_length[2]',
            'grade' => 'permit_empty|string|max_length[30]',
            'year'  => 'permit_empty|string|max_length[20]',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $model->update($id, [
            'name'  => $data['name'],
            'grade' => $data['grade'] ?? null,
            'year'  => $data['year'] ?? null,
        ]);

        return $this->respond(['ok' => true]);
    }

    public function delete(int $id)
    {
        $model = model(ClassModel::class);

        if ($model->find($id) === null) {
            return $this->failNotFound('Class not found');
        }

        $model->delete($id);

        return $this->respondDeleted(['ok' => true]);
    }
}
