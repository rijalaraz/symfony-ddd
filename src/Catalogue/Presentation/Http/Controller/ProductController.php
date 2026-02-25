<?php

namespace App\Catalogue\Presentation\Http\Controller;

use App\Catalogue\Application\Command\CreateProductCommand;
use App\Catalogue\Application\Command\Handler\CreateProductCommandHandler;
use App\Catalogue\Application\Query\GetProductsQuery;
use App\Catalogue\Application\Query\Handler\GetProductsQueryHandler;
use App\Catalogue\Dto\ProductDto;
use App\Catalogue\Mapper\ProductMapper;
use App\SharedKernel\Http\ResponseEnvelope;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[Route('/api/products')]
class ProductController extends AbstractController
{

    #[Route('', name: 'products.list', methods: ['GET'])]
    #[OA\Parameter(
        name: 'only_available',
        in: 'query',
        description: 'Filter to return only products that are currently available (in stock).',
        required: false,
        schema: new OA\Schema(type: 'boolean')
    )]
    #[OA\Parameter(
        name: 'max_price',
        in: 'query',
        description: 'Filter to return only products with a price less than or equal to the specified value.',
        required: false,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_OK,
        description: 'List of products',
        content: new OA\JsonContent(
            type:'object',
            properties: [
                new OA\Property(
                    property: 'data',
                    type:'array',
                    items: new OA\Items(ref: new Model(type: ProductDto::class, groups: ['product:list']))
                )
            ]
        )
    )]
    #[OA\Tag(name: 'Products')]
    public function listProducts(GetProductsQueryHandler $handler, Request $request, SerializerInterface $serializer, ProductMapper $productMapper): JsonResponse
    {
        $onlyAvailable = filter_var($request->query->get('only_available', 'false'), FILTER_VALIDATE_BOOLEAN);

        $query = new GetProductsQuery($onlyAvailable, $request->query->get('max_price'));

        $products = $handler($query);

        return $this->json([
            'data' => $productMapper->toDto($products)
        ], context: [
            'groups' => ['product:list']
        ]);

        // $json = $serializer->serialize([
        //     'data' => $products
        // ],'json', [
        //     'groups' => ['product:list']
        // ]);
        // return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);

        // $envelope = ResponseEnvelope::success($products);
        // return new JsonResponse($envelope->body, $envelope->status);
    }

    #[Route('', name: 'products.create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Product data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: ProductDto::class, groups: ['product:create']))
    )]
    #[OA\Response(
        response: JsonResponse::HTTP_CREATED,
        description: 'Product created successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', ref: new Model(type: ProductDto::class, groups: ['product:detail'])),
            ]
        )
    )]
    #[OA\Tag(name: 'Products')]
    public function create(CreateProductCommandHandler $handler, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new CreateProductCommand(
            Uuid::v7()->toString(), $data['name'] ?? '', (int)($data['price'] ?? 0), (int)($data['on_hand'] ?? 0)
        );
        $product = $handler($command);
        $envelope = ResponseEnvelope::success(['id' => $product->getId()], JsonResponse::HTTP_CREATED);

        return new JsonResponse($envelope->body, $envelope->status);
    }
}
