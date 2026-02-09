<?php

namespace App\Controllers\Api;

use App\Models\StudentModel;

class StudentsController extends BaseApiController
{
    public function index()
    {
        $model = model(StudentModel::class);

        $classId = $this->request->getGet('class_id');

        if ($classId !== null && $classId !== '') {
            $model->where('class_id', (int) $classId);
        }

        return $this->respond([
            'data' => $model->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function show(int $id)
    {
        $model = model(StudentModel::class);
        $row   = $model->find($id);

        if ($row === null) {
            return $this->failNotFound('Student not found');
        }

        return $this->respond($row);
    }

    public function create()
    {
        $data = $this->getJsonBody();

        $rules = [
            'nis'        => 'permit_empty|string|max_length[30]',
            'name'       => 'required|string|min_length[2]',
            'class_id'   => 'permit_empty|is_natural_no_zero',
            'gender'     => 'permit_empty|string|max_length[10]',
            'birth_date' => 'permit_empty|valid_date',
            'active'     => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $model = model(StudentModel::class);
        $id    = $model->insert([
            'nis'        => $data['nis'] ?? null,
            'name'       => $data['name'],
            'class_id'   => isset($data['class_id']) && $data['class_id'] !== '' ? (int) $data['class_id'] : null,
            'gender'     => $data['gender'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'active'     => isset($data['active']) ? (int) $data['active'] : 1,
        ], true);

        return $this->respondCreated(['id' => $id]);
    }

    public function update(int $id)
    {
        $data  = $this->getJsonBody();
        $model = model(StudentModel::class);

        if ($model->find($id) === null) {
            return $this->failNotFound('Student not found');
        }

        $rules = [
            'nis'        => 'permit_empty|string|max_length[30]',
            'name'       => 'required|string|min_length[2]',
            'class_id'   => 'permit_empty|is_natural_no_zero',
            'gender'     => 'permit_empty|string|max_length[10]',
            'birth_date' => 'permit_empty|valid_date',
            'active'     => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->respondValidationErrors($this->validator->getErrors());
        }

        $model->update($id, [
            'nis'        => $data['nis'] ?? null,
            'name'       => $data['name'],
            'class_id'   => isset($data['class_id']) && $data['class_id'] !== '' ? (int) $data['class_id'] : null,
            'gender'     => $data['gender'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'active'     => isset($data['active']) ? (int) $data['active'] : 1,
        ]);

        return $this->respond(['ok' => true]);
    }

    public function delete(int $id)
    {
        $model = model(StudentModel::class);

        if ($model->find($id) === null) {
            return $this->failNotFound('Student not found');
        }

        $model->delete($id);

        return $this->respondDeleted(['ok' => true]);
    }
}
