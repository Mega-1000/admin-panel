<?php

namespace App\Http\Controllers\Api;

use App\Repositories\FaqRepository;
use Illuminate\Http\Request;

/**
 * Klasa kontrolera obsługująca
 * @package App\Http\Controllers\Api
 *
 * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
 */
class FaqController
{
    use ApiResponsesTrait;

    /**
     * @var FaqRepository
     */
    protected $repository;

    /**
     * FaqController constructor.
     * @param FaqRepository $faqRepository
     */
    public function __construct(FaqRepository $faqRepository)
    {
        $this->repository = $faqRepository;
    }

    /**
     * Akcja do zapisywania pytań
     *
     * @param Request $request
     *
     * @author Norbert Grzechnik <norbert.grzechnik@netro42.digital>
     */
    public function savePath(Request $request)
    {
        $this->repository->create(
            [
                'category' => $request->get('category'),
                'questions' => json_encode($request->get('questions')),
            ]
        );
    }

    public function getQuestions()
    {
        return $this->repository->all(['category','questions']);
    }
}