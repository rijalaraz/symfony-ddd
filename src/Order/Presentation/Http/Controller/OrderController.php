<?php

namespace App\Order\Presentation\Http\Controller;

use App\Order\Application\Command\CreateOrderCommand;
use App\Order\Application\Command\Handler\CreateOrderCommandHandler;
use App\Order\Application\Command\Handler\FulfillOrderCommandHandler;
use App\Order\Application\Query\GetOrdersQuery;
use App\Order\Application\Query\Handler\GetOrdersQueryHandler;
use App\SharedKernel\Http\ResponseEnvelope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[Route('/api/orders')]
class OrderController
{

    #[Route('', name: 'orders.list', methods: ['GET'])]
    public function listOrders(GetOrdersQueryHandler $handler, Request $request): JsonResponse
    {
        $envelope = ResponseEnvelope::success($handler(new GetOrdersQuery($request->get('status'))));

        return new JsonResponse($envelope->body, $envelope->status);
    }

    #[Route('', name: 'orders.create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Order creation payload',
        required: true,
        content: new OA\JsonContent(
            type:'object',
            properties: [
                new OA\Property(property: 'amount_to_pay', type: 'integer', description: 'Total amount to pay for the order in minor units (e.g., cents).'),
                new OA\Property(
                    property: 'products',
                    type:'array',
                    description: 'List of products included in the order.',
                    items: new OA\Items(
                        type:'object',
                        properties: [
                            new OA\Property(property: 'product_id', type: 'string', description: 'ID of the product.'),
                            new OA\Property(property: 'name', type: 'string', description: 'Name of the product.'),
                            new OA\Property(property: 'quantity', type: 'integer', description: 'Quantity of the product.'),
                            new OA\Property(property: 'price', type: 'integer', description: 'Price of the products in minor units (e.g., cents).')
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Order created successfully',
        content: new OA\JsonContent(
            type:'object',
            properties: [
                new OA\Property(property: 'id', type: 'string', description: 'ID of the created order.')
            ]
        )
    )]
    #[OA\Tag(name: 'Orders')]
    public function create(CreateOrderCommandHandler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateOrderCommand(Uuid::v7()->toString(), $data['amount_to_pay'] ?? 0, $data['products'] ?? []);

        $order = $handler($command);
        $envelope = ResponseEnvelope::success(['id' => $order->getId()], JsonResponse::HTTP_CREATED);
        return new JsonResponse($envelope->body, $envelope->status);
    }

    #[Route('/{orderId}/fulfill', name: 'orders.fulfill', methods: ['POST'])]
    public function fulfill(FulfillOrderCommandHandler $handler, Request $request): JsonResponse
    {
        $handler($request->get('orderId'));
        $envelope = ResponseEnvelope::success();
        return new JsonResponse($envelope->body, $envelope->status);
    }
}
