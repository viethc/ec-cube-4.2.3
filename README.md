# ec-cube-4.2.3
Learning EC-CUBE version 4.2.3

## Run docker
`docker-compose -f docker-compose.yml -f docker-compose.pgsql.yml -f docker-compose.pgadmin.yml -f docker-compose.dev.yml up -d`

## Xóa tất cả container đã dừng và tất cả các image không sử dụng:
`docker system prune -a`

## Xóa tất cả container:
`docker rm $(docker ps -a -q)`

## Xóa tất cả image:
`docker rmi $(docker images -a -q)`

## Xóa tất cả các volumes không sử dụng:
`docker volume ls -f dangling=true`
`docker volume prune`

## Xóa một volume cụ thể:
`docker volume rm <volume_name>`

## CMD
https://doc4.ec-cube.net/quickstart/cli