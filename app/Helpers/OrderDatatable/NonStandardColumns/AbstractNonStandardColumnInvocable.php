<?php

namespace App\Helpers\OrderDatatable\NonStandardColumns;

abstract class AbstractNonStandardColumnInvocable
{
    protected string $view;

    public function __construct(
        protected ?array $data = null
    ) {}

    /**
     * Renders view for non-standard column
     *
     * @param array $order
     * @return string
     */
    public function __invoke(array $order): string
    {
        return view($this->view, $this->getData($order) + ['order' => $order])->render();
    }

    /**
     * Prepares data for view
     *
     * @param array $order
     * @return array
     */
    protected abstract function getData(array $order): array;

}
