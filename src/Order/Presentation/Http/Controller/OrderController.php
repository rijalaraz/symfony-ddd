<?php

namespace App\Order\Presentation\Http\Controller;

use App\Order\Application\Command\CreateOrderCommand;
use App\Order\Application\Command\Handler\CreateOrderCommandHandler;
use App\Order\Application\Command\Handler\FulfillOrderCommandHandler;
use App\Order\Application\Query\GetOrdersQuery;
use App\Order\Application\Query\Handler\GetOrdersQueryHandler;
use App\Order\Domain\Enum\OrderStatus;
use App\Order\Dto\OrderDto;
use App\Order\Mapper\OrderMapper;
use App\SharedKernel\Http\ResponseEnvelope;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[Route('/api/orders')]
class OrderController extends AbstractController
{

    #[Route('', name: 'orders.list', methods: ['GET'])]
    #[OA\Parameter(
        name: 'status',
        in: 'query',
        description: 'Filter to return only orders with the specified status (e.g., pending, fulfilled).',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            enum: [OrderStatus::PENDING, OrderStatus::RESERVED, OrderStatus::FULFILLED, OrderStatus::FAILED],
        )
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'List of orders',
        content: new OA\JsonContent(
            type:'object',
            properties: [
                new OA\Property(
                    property: 'data',
                    type:'array',
                    items: new OA\Items(ref: new Model(type: OrderDto::class, groups: ['order:list']))
                )
            ]
        )
    )]
    #[OA\Tag(name: 'Orders')]
    public function listOrders(GetOrdersQueryHandler $handler, Request $request, OrderMapper $orderMapper): JsonResponse
    {
        $query = new GetOrdersQuery($request->query->get('status'));

        $orders = $handler($query);

        return $this->json([
            'data' => $orderMapper->toDto($orders)
        ], context:[
            'groups' => ['order:list']
        ]);

        // $envelope = ResponseEnvelope::success($orders);
        // return new JsonResponse($envelope->body, $envelope->status);
    }

    #[Route('', name: 'orders.create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Order creation payload',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: OrderDto::class, groups: ['order:create']))
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Order created successfully',
        content: new OA\JsonContent(
            type:'object',
            properties: [
                new OA\Property(property: 'data', ref:new Model(type:OrderDto::class, groups: ['order:detail']))
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
        $handler($request->attributes->get('orderId'));
        $envelope = ResponseEnvelope::success();
        return new JsonResponse($envelope->body, $envelope->status);
    }
}
