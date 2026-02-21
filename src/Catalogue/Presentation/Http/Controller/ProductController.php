<?php

namespace App\Catalogue\Presentation\Http\Controller;

use App\Catalogue\Application\Command\CreateProductCommand;
use App\Catalogue\Application\Command\Handler\CreateProductCommandHandler;
use App\Catalogue\Application\Query\GetProductsQuery;
use App\Catalogue\Application\Query\Handler\GetProductsQueryHandler;
use App\SharedKernel\Http\ResponseEnvelope;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[Route('/api/products')]
class ProductController
{

    #[Route('', name: 'products.list', methods: ['GET'])]
    public function listProducts(GetProductsQueryHandler $handler, Request $request): JsonResponse
    {
        $envelope = ResponseEnvelope::success($handler(new GetProductsQuery($request->get('only_available', false), $request->get('max_price'))));
        return new JsonResponse($envelope->body, $envelope->status);
    }

    #[Route('', name: 'products.create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Product data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateProductCommand::class, groups: ['product:create']))
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Product created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', ref: new Model(type: CreateProductCommand::class, groups: ['product:detail'])),
            ]
        )
    )]
    public function create(CreateProductCommandHandler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new CreateProductCommand(
            Uuid::v7()->toString(), $data['name'] ?? '', (int)($data['price'] ?? 0), (int)($data['onHand'] ?? 0)
        );
        $product = $handler($command);
        $envelope = ResponseEnvelope::success(['id' => $product->getId()], JsonResponse::HTTP_CREATED);

        return new JsonResponse($envelope->body, $envelope->status);
    }
}
